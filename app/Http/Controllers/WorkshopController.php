<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Workshop;
use App\Models\WorkshopParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    /**
     * Display the workshop catalogue for every role.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $canManage = $user->isAdmin();

        $query = Workshop::withCount([
            'participants',
            'participants as confirmed_participants_count' => fn ($q) => $q->where('confirmation_status', 'confirmed'),
            'evaluations',
        ])->with('creator');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $myWorkshopIds = $user->employee_id
            ? WorkshopParticipant::where('employee_id', $user->employee_id)->pluck('workshop_id')->all()
            : [];

        // Participants may only see the workshops they are enrolled in.
        if ($user->isKaryawan()) {
            $query->whereIn('id', $myWorkshopIds ?: [0]);
        }

        $workshops = $query->orderByDesc('year')->orderByDesc('batch')->paginate(9)->withQueryString();

        return view('workshops.index', compact('workshops', 'myWorkshopIds', 'canManage'));
    }

    /**
     * Show a workshop with its participant and evaluation summary.
     */
    public function show(Request $request, Workshop $workshop): View
    {
        $workshop->load([
            'participants.employee.plant',
            'participants.employee.department',
            'participants.evaluation',
            'participants.evidences.reviewer',
            'evaluations.evaluator',
            'scoreReports',
        ]);

        $user = $request->user();
        $canManage = $user->isAdmin();
        $canEvaluate = $user->isOmdd();

        $myParticipant = $user->employee_id
            ? $workshop->participants->firstWhere('employee_id', $user->employee_id)
            : null;

        // Participants may only open workshops they have joined.
        if ($user->isKaryawan() && ! $myParticipant) {
            abort(403, 'Anda hanya dapat melihat workshop yang Anda ikuti.');
        }

        // Number of weeks the workshop runs, used for the weekly evidence tracker.
        $totalWeeks = 1;
        if ($workshop->start_date && $workshop->end_date) {
            $days = $workshop->start_date->diffInDays($workshop->end_date) + 1;
            $totalWeeks = max(1, (int) ceil($days / 7));
        }

        $eligibleEmployees = $canManage
            ? Employee::where('status', 'active')
                ->whereNotIn('id', $workshop->participants->pluck('employee_id'))
                ->orderBy('name')
                ->get()
            : collect();

        return view('workshops.show', compact(
            'workshop',
            'canManage',
            'canEvaluate',
            'myParticipant',
            'eligibleEmployees',
            'totalWeeks'
        ));
    }

    /**
     * Show the workshop creation form.
     */
    public function create(): View
    {
        return view('workshops.create');
    }

    /**
     * Store a new workshop batch.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $validated['created_by'] = $request->user()->id;

        $workshop = Workshop::create($validated);

        return redirect()->route('workshops.show', $workshop)
            ->with('success', "Workshop {$workshop->title} berhasil dibuat.");
    }

    /**
     * Show the workshop edit form.
     */
    public function edit(Workshop $workshop): View
    {
        return view('workshops.edit', compact('workshop'));
    }

    /**
     * Update a workshop batch.
     */
    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate($this->rules($workshop->id));
        $workshop->update($validated);

        return redirect()->route('workshops.show', $workshop)
            ->with('success', "Workshop {$workshop->title} berhasil diperbarui.");
    }

    /**
     * Delete a workshop batch and its dependent stage records.
     */
    public function destroy(Workshop $workshop): RedirectResponse
    {
        $title = $workshop->title;
        $workshop->delete();

        return redirect()->route('workshops.index')
            ->with('success', "Workshop {$title} berhasil dihapus.");
    }

    /**
     * Admin adds an employee as workshop participant.
     */
    public function addParticipant(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('workshop_participants', 'employee_id')->where('workshop_id', $workshop->id),
            ],
        ]);

        $participant = WorkshopParticipant::create([
            'workshop_id' => $workshop->id,
            'employee_id' => $validated['employee_id'],
            'confirmed_by' => $request->user()->id,
            'confirmation_status' => 'confirmed',
        ]);

        return back()->with('success', "Peserta {$participant->employee->name} berhasil ditambahkan.");
    }

    /**
     * Admin confirms a pending participant.
     */
    public function confirmParticipant(Request $request, Workshop $workshop, WorkshopParticipant $participant): RedirectResponse
    {
        abort_unless((int) $participant->workshop_id === (int) $workshop->id, 404);

        $participant->update([
            'confirmation_status' => 'confirmed',
            'confirmed_by' => $request->user()->id,
        ]);

        return back()->with('success', "Peserta {$participant->employee->name} dikonfirmasi.");
    }

    /**
     * Admin removes a participant.
     */
    public function removeParticipant(Workshop $workshop, WorkshopParticipant $participant): RedirectResponse
    {
        abort_unless((int) $participant->workshop_id === (int) $workshop->id, 404);
        $participant->delete();

        return back()->with('success', 'Peserta dikeluarkan dari workshop.');
    }

    /**
     * Validation rules shared by create & update.
     */
    private function rules(?int $ignoreId = null): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'batch' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('workshops', 'batch')->where('year', request('year'))->ignore($ignoreId),
            ],
            'title' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'string', 'in:draft,scheduled,ongoing,completed'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
