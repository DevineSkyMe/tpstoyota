<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Message;
use App\Models\Plant;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopEvaluation;
use App\Models\WorkshopHrdReview;
use App\Models\WorkshopParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateController extends Controller
{
    /**
     * Display the candidate promotion pipeline with search and filters.
     */
    public function index(Request $request): View
    {
        $query = WorkshopCandidate::with([
            'employee.plant',
            'employee.department',
            'employee.activityPlans',
            'hrdReview',
            'recommendedBy',
        ]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('target_level')) {
            $query->where('target_tps_level', $request->input('target_level'));
        }

        if ($request->filled('plant_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('plant_id', $request->input('plant_id'));
            });
        }

        if ($request->filled('status')) {
            $query->where('tlc_status', $request->input('status'));
        }

        // HRD starts recommendations from their own queue by default.
        if ($request->user()->isHrd() && ! $request->filled('status')) {
            $query->whereIn('tlc_status', ['recommended', 'approved']);
        }

        $candidates = $query->latest()->paginate(9)->withQueryString();

        $plants = Plant::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        $totalCandidates = WorkshopCandidate::count();
        $underEvaluation = WorkshopCandidate::where('tlc_status', 'recommended')->count();
        $kp4Candidates = WorkshopCandidate::where('target_tps_level', 'KP4')->count();
        $kp3Candidates = WorkshopCandidate::where('target_tps_level', 'KP3')->count();

        // Only HRD (and Admin as backup) may create a recommendation.
        $canRecommend = $request->user()->hasRole(UserAccount::ROLE_HRD, UserAccount::ROLE_ADMIN);

        return view('candidates.index', compact(
            'candidates',
            'plants',
            'departments',
            'totalCandidates',
            'underEvaluation',
            'kp4Candidates',
            'kp3Candidates',
            'canRecommend'
        ));
    }

    /**
     * Show the HRD recommendation form.
     */
    public function create(Request $request): View
    {
        $employeeIds = WorkshopCandidate::query()
            ->whereIn('tlc_status', ['recommended', 'approved'])
            ->pluck('employee_id');

        $employees = Employee::with(['plant', 'department'])
            ->where('status', 'active')
            ->whereNotIn('id', $employeeIds)
            ->orderBy('name')
            ->get()
            ->filter(fn (Employee $employee) => $employee->canPromote());

        return view('candidates.create', compact('employees'));
    }

    /**
     * Store an HRD recommendation for the next workshop level.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'target_tps_level' => 'nullable|string|in:KP4,KP3,KP2,KP1,Advance',
            'tlc_reason' => 'required|string|max:1000',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $target = $validated['target_tps_level'] ?: $employee->getNextTpsLevel() ?: $employee->current_tps_level;

        $candidate = WorkshopCandidate::updateOrCreate(
            ['employee_id' => $employee->id, 'target_tps_level' => $target],
            [
                'recommended_by' => $request->user()->id,
                'tlc_reason' => $validated['tlc_reason'],
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => now(),
            ]
        );

        WorkshopHrdReview::updateOrCreate(
            ['candidate_id' => $candidate->id],
            [
                'hrd_id' => $request->user()->id,
                'hrd_status' => 'recommended',
                'hrd_note' => $validated['tlc_reason'],
                'reviewed_at' => now(),
            ]
        );

        // Notify Admin that a new recommendation is waiting for confirmation.
        $admin = UserAccount::where('role', UserAccount::ROLE_ADMIN)->orderBy('id')->first();

        if ($admin) {
            Message::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $admin->id,
                'related_type' => 'workshop_candidate',
                'related_id' => $candidate->id,
                'message' => "HRD merekomendasikan {$employee->name} (NIK {$employee->username}) untuk workshop tingkat {$target}. Mohon ditinjau dan ditambahkan sebagai peserta.",
                'is_read' => false,
            ]);
        }

        return redirect()->route('candidates.index')
            ->with('success', "Rekomendasi HRD untuk {$employee->name} berhasil dikirim ke Admin.");
    }

    /**
     * Admin confirms an HRD recommendation.
     */
    public function approve(Request $request, int $id): RedirectResponse
    {
        $candidate = WorkshopCandidate::with('employee')->findOrFail($id);
        $candidate->update(['tlc_status' => 'approved']);

        return redirect()->route('candidates.index')
            ->with('success', "Kandidat {$candidate->employee->name} disetujui. Tambahkan sebagai peserta workshop dari menu Workshop.");
    }

    /**
     * Display candidate detail and OMDD Workshop Assessment page.
     */
    public function show(int $id): View
    {
        $candidate = WorkshopCandidate::with([
            'employee.plant',
            'employee.division',
            'employee.department',
            'employee.activityPlans',
            'employee.workshopEvaluations',
            'hrdReview',
            'recommendedBy',
        ])->where('id', $id)
            ->orWhere('employee_id', $id)
            ->firstOrFail();

        $employee = $candidate->employee;

        $assessor = WorkshopEvaluation::with('evaluator')
            ->where('employee_id', $employee->id)
            ->latest()
            ->first()?->evaluator
            ?? UserAccount::where('role', UserAccount::ROLE_OMDD)->first();

        $workshop = Workshop::where('status', '!=', 'completed')->latest('start_date')->first()
            ?? Workshop::latest('start_date')->first();

        $evaluation = WorkshopEvaluation::where('employee_id', $employee->id)
            ->latest()
            ->first();

        // Rubric scores are stored on a 1-5 scale.
        $scoreProblem = $evaluation->ws_1 ?? 0;
        $scoreRca = $evaluation->ws_2 ?? 0;
        $scoreYokoten = $evaluation->ws_3 ?? 0;
        $scoreCoaching = $evaluation->ws_4 ?? 0;

        $compositeScore = $evaluation?->composite_score ?? 0.0;
        $passingBenchmark = WorkshopEvaluation::PASSING_BENCHMARK;
        $isPassed = $compositeScore >= $passingBenchmark;

        $jishukenCycles = $employee->activityPlans->count();
        $kaizenYield = $employee->activityPlans->where('status', 'completed')->count() . ' selesai';
        $safetyScore = $employee->activityPlans->avg('progress_pct');
        $safetyScore = $safetyScore ? round($safetyScore) . '/100' : 'N/A';
        $tpsMastery = $compositeScore > 0 ? (int) round($compositeScore) : 0;

        $canAssess = request()->user()->isOmdd();

        return view('candidates.show', compact(
            'candidate',
            'employee',
            'assessor',
            'workshop',
            'evaluation',
            'scoreProblem',
            'scoreRca',
            'scoreYokoten',
            'scoreCoaching',
            'compositeScore',
            'passingBenchmark',
            'isPassed',
            'jishukenCycles',
            'kaizenYield',
            'safetyScore',
            'tpsMastery',
            'canAssess'
        ));
    }

    /**
     * Submit and persist OMDD assessment scoring.
     */
    public function submitAssessment(Request $request, int $id): RedirectResponse
    {
        $candidate = WorkshopCandidate::with('employee')->findOrFail($id);
        $employee = $candidate->employee;

        $validated = $request->validate([
            'ws_1' => 'required|integer|min:1|max:5',
            'ws_2' => 'required|integer|min:1|max:5',
            'ws_3' => 'required|integer|min:1|max:5',
            'ws_4' => 'required|integer|min:1|max:5',
            'ws_5' => 'required|integer|min:1|max:5',
            'ws_6' => 'required|integer|min:1|max:5',
            'ws_7' => 'required|integer|min:1|max:5',
            'final_decision' => 'required|string|in:pass,fail',
            'notes' => 'nullable|string|max:1000',
        ]);

        $workshop = Workshop::where('status', '!=', 'completed')->latest('start_date')->first()
            ?? Workshop::latest('start_date')->firstOrFail();

        $participant = WorkshopParticipant::firstOrCreate(
            ['workshop_id' => $workshop->id, 'employee_id' => $employee->id],
            [
                'candidate_id' => $candidate->id,
                'confirmed_by' => $request->user()->id,
                'confirmation_status' => 'confirmed',
            ]
        );

        $evaluation = WorkshopEvaluation::updateOrCreate(
            ['employee_id' => $employee->id, 'workshop_id' => $workshop->id],
            [
                'participant_id' => $participant->id,
                'ws_1' => $validated['ws_1'],
                'ws_2' => $validated['ws_2'],
                'ws_3' => $validated['ws_3'],
                'ws_4' => $validated['ws_4'],
                'ws_5' => $validated['ws_5'],
                'ws_6' => $validated['ws_6'],
                'ws_7' => $validated['ws_7'],
                'final_decision' => $validated['final_decision'],
                'notes' => $validated['notes'] ?? '',
                'evaluated_by' => $request->user()->display_name,
                'evaluated_by_id' => $request->user()->id,
                'evaluated_at' => now(),
            ]
        );

        if ($validated['final_decision'] === 'pass') {
            $employee->promoteToNextLevel();
            $candidate->update(['tlc_status' => 'approved']);
            $participant->update(['confirmation_status' => 'confirmed']);

            if ($candidate->hrdReview) {
                $candidate->hrdReview->update(['hrd_status' => 'recommended']);
            }
        }

        return redirect()->route('candidates.show', $candidate->id)
            ->with('success', "Penilaian OMDD untuk {$employee->name} disimpan. Composite score: {$evaluation->composite_score}% ({$validated['final_decision']}).");
    }
}
