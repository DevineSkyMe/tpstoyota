<?php

namespace App\Http\Controllers;

use App\Models\ActivityFollowup;
use App\Models\ActivityPlan;
use App\Models\ActivityProgress;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display the Activity Monitoring Log page with filters, KPIs and pagination.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = ActivityPlan::with(['employee.department', 'employee.plant', 'followups'])
            ->withCount([
                'progressUpdates as pending_progress_count' => fn ($q) => $q->where('approval_status', ActivityProgress::STATUS_PENDING),
                'progressUpdates as approved_progress_count' => fn ($q) => $q->where('approval_status', ActivityProgress::STATUS_APPROVED),
            ]);

        // Karyawan only sees their own activity logs.
        if ($user->isKaryawan()) {
            $employeeId = $user->employee_id;

            if (! $employeeId) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('employee_id', $employeeId);
            }
        }

        // Search by Project ID, Agent Name, NIK, or Kaizen Topic
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($empQ) use ($search) {
                        $empQ->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->input('department_id'));
            });
        }

        // Filter by TPS Level
        if ($request->filled('level')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('current_tps_level', $request->input('level'));
            });
        }

        // Filter by Status (not_started, in_progress, delay, completed)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('planned_start', [$request->input('start_date'), $request->input('end_date')]);
        }

        $perPage = max(5, min(100, $request->integer('rows', $request->integer('per_page', 10))));
        $activities = $query->latest()->paginate($perPage)->withQueryString();

        // KPI Counts follow the same visibility scope.
        $scope = ActivityPlan::query();
        if ($user->isKaryawan()) {
            $scope->where('employee_id', $user->employee_id ?? 0);
        }

        $totalActivities = (clone $scope)->count();
        $inProgressActivities = (clone $scope)->where('status', 'in_progress')->count();
        $completedActivities = (clone $scope)->where('status', 'completed')->count();

        $thisWeekNew = (clone $scope)->where('created_at', '>=', now()->subDays(7))->count();
        $reviewsDue = (clone $scope)->where(function ($q) {
            $q->where('status', 'delay')
                ->orWhere(function ($sub) {
                    $sub->where('status', 'in_progress')->where('progress_pct', '>=', 70);
                });
        })->count();

        $completionRate = $totalActivities > 0
            ? round(($completedActivities / $totalActivities) * 100, 1)
            : 0;

        // Progress updates still awaiting an Admin decision (ACC).
        $pendingReviews = ActivityProgress::query()
            ->where('approval_status', ActivityProgress::STATUS_PENDING)
            ->when($user->isKaryawan(), function ($q) use ($user) {
                $q->whereHas('activityPlan', fn ($a) => $a->where('employee_id', $user->employee_id ?? 0));
            })
            ->count();

        // Dropdowns & Filters Master Data
        $departments = Department::orderBy('name')->get();
        $tpsLevels = ['KP4', 'KP3', 'Advance', 'KP2', 'KP1'];

        // Current Genba Milestone
        $activeMilestone = Workshop::whereIn('status', ['scheduled', 'ongoing'])->orderBy('start_date')->first()
            ?? Workshop::latest('start_date')->first();

        return view('activity.index', compact(
            'activities',
            'totalActivities',
            'inProgressActivities',
            'completedActivities',
            'thisWeekNew',
            'reviewsDue',
            'completionRate',
            'pendingReviews',
            'departments',
            'tpsLevels',
            'activeMilestone'
        ));
    }

    /**
     * Show the form for creating a new activity plan.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $lockedEmployee = $user->isKaryawan() ? $user->employee : null;

        if ($user->isKaryawan() && ! $lockedEmployee) {
            abort(403, 'Akun Anda belum terhubung ke data karyawan. Hubungi Admin.');
        }

        $employees = $lockedEmployee
            ? collect([$lockedEmployee])
            : Employee::where('status', 'active')->orderBy('name')->get();

        $departments = Department::orderBy('name')->get();

        return view('activity.create', compact('employees', 'departments', 'lockedEmployee'));
    }

    /**
     * Store a newly created activity plan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'employee_id' => 'nullable|exists:employees,id',
            'planned_start' => 'required|date',
            'planned_end' => 'required|date|after_or_equal:planned_start',
            'status' => 'required|string|in:not_started,in_progress,delay,completed',
            'progress_pct' => 'required|integer|min:0|max:100',
            'pic' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'batch' => 'nullable|string|max:50',
        ]);

        $employee = $user->isKaryawan()
            ? $user->employee
            : Employee::find($validated['employee_id'] ?? null);

        if (! $employee) {
            return back()->withInput()->withErrors(['employee_id' => 'Key Person / PIC wajib dipilih.']);
        }

        $validated['employee_id'] = $employee->id;
        $validated['pic'] = ($validated['pic'] ?? null) ?: $employee->name;

        $activity = ActivityPlan::create($validated);

        return redirect()->route('activity.show', $activity->id)
            ->with('success', "Proyek Kaizen '{$activity->title}' berhasil didaftarkan ke Activity Log.");
    }

    /**
     * Display the specified activity plan with milestones & progress logs.
     */
    public function show(Request $request, int $id): View
    {
        $activity = ActivityPlan::with([
            'employee.department',
            'employee.plant',
            'progressUpdates' => fn ($q) => $q->with(['submittedBy.employee', 'reviewer'])->latest(),
            'followups.admin',
        ])->findOrFail($id);

        $this->authorizeView($request, $activity);

        return view('activity.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified activity plan.
     */
    public function edit(int $id): View
    {
        $activity = ActivityPlan::findOrFail($id);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('activity.edit', compact('activity', 'employees', 'departments'));
    }

    /**
     * Update the specified activity plan in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $activity = ActivityPlan::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'employee_id' => 'required|exists:employees,id',
            'planned_start' => 'required|date',
            'planned_end' => 'required|date|after_or_equal:planned_start',
            'actual_start' => 'nullable|date',
            'actual_end' => 'nullable|date',
            'status' => 'required|string|in:not_started,in_progress,delay,completed',
            'progress_pct' => 'required|integer|min:0|max:100',
            'pic' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'batch' => 'nullable|string|max:50',
        ]);

        $activity->update($validated);

        return redirect()->route('activity.show', $activity->id)
            ->with('success', "Proyek Kaizen '{$activity->title}' berhasil diperbarui.");
    }

    /**
     * Remove the specified activity plan from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $activity = ActivityPlan::findOrFail($id);
        $title = $activity->title;
        $activity->delete();

        return redirect()->route('activity')
            ->with('success', "Proyek Kaizen '{$title}' berhasil dihapus.");
    }

    /**
     * Admin records a monitoring / follow-up note for an activity.
     */
    public function storeFollowup(Request $request, int $id): RedirectResponse
    {
        $activity = ActivityPlan::findOrFail($id);

        $validated = $request->validate([
            'followup_note' => 'required|string|max:1000',
            'status' => 'nullable|in:pending,in_review,resolved,escalated',
        ]);

        ActivityFollowup::create([
            'activity_plan_id' => $activity->id,
            'admin_id' => $request->user()->id,
            'followup_note' => $validated['followup_note'],
            'status' => $validated['status'] ?? 'pending',
        ]);

        return back()->with('success', 'Catatan monitoring berhasil ditambahkan.');
    }

    /**
     * Karyawan may only inspect their own activity records.
     */
    private function authorizeView(Request $request, ActivityPlan $activity): void
    {
        $user = $request->user();

        if ($user->isKaryawan() && (int) $activity->employee_id !== (int) $user->employee_id) {
            abort(403, 'Anda hanya dapat melihat activity milik sendiri.');
        }
    }
}
