<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityFollowup;
use App\Models\ActivityPlan;
use App\Models\ActivityProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityPlanApiController extends BaseApiController
{
    /**
     * Display a listing of activities (supporting search, department, level, status, dates).
     */
    public function index(Request $request)
    {
        if ($request->acceptsHtml() && ! $request->expectsJson()) {
            return redirect()->route('activity', $request->query());
        }

        $query = ActivityPlan::with(['employee.plant', 'employee.department', 'progressUpdates', 'followups']);

        // Search by project ID, title, agent name, or category
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
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

        // Filter by Level
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

        $perPage = $request->integer('per_page', 10);
        $activities = $query->latest()->paginate($perPage);

        return $this->sendResponse($activities, 'Activities retrieved successfully');
    }

    /**
     * Store a newly created activity plan.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'planned_start' => 'required|date',
            'planned_end' => 'required|date|after_or_equal:planned_start',
            'actual_start' => 'nullable|date',
            'actual_end' => 'nullable|date',
            'status' => 'nullable|in:not_started,in_progress,delay,completed',
            'progress_pct' => 'nullable|integer|min:0|max:100',
            'pic' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'batch' => 'nullable|string|max:50',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $activity = ActivityPlan::create($request->all());

        return $this->sendResponse($activity->load('employee'), 'Activity plan created successfully', 201);
    }

    /**
     * Display the specified activity plan.
     */
    public function show(int $id)
    {
        if (request()->acceptsHtml() && ! request()->expectsJson()) {
            return redirect()->route('activity.show', $id);
        }

        $activity = ActivityPlan::with([
            'employee.plant',
            'employee.department',
            'progressUpdates',
            'followups.admin'
        ])->find($id);

        if (!$activity) {
            return $this->sendError('Activity not found', [], 404);
        }

        return $this->sendResponse($activity, 'Activity retrieved successfully');
    }

    /**
     * Update the specified activity plan.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $activity = ActivityPlan::find($id);

        if (!$activity) {
            return $this->sendError('Activity not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:200',
            'status' => 'nullable|in:not_started,in_progress,delay,completed',
            'progress_pct' => 'nullable|integer|min:0|max:100',
            'actual_start' => 'nullable|date',
            'actual_end' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $activity->update($request->all());

        return $this->sendResponse($activity->fresh(['employee', 'progressUpdates']), 'Activity plan updated successfully');
    }

    /**
     * Remove the specified activity plan.
     */
    public function destroy(int $id): JsonResponse
    {
        $activity = ActivityPlan::find($id);

        if (!$activity) {
            return $this->sendError('Activity not found', [], 404);
        }

        $activity->delete();

        return $this->sendResponse(null, 'Activity deleted successfully');
    }

    /**
     * Add a progress update to an activity.
     */
    public function addProgress(Request $request, int $id): JsonResponse
    {
        $activity = ActivityPlan::find($id);

        if (!$activity) {
            return $this->sendError('Activity not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'status_update' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'new_progress_pct' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $progress = ActivityProgress::create([
            'activity_plan_id' => $activity->id,
            'status_update' => $request->input('status_update'),
            'description' => $request->input('description'),
            'image_path' => $request->input('image_path'),
        ]);

        if ($request->filled('new_progress_pct')) {
            $activity->update([
                'progress_pct' => $request->integer('new_progress_pct'),
                'status' => $request->integer('new_progress_pct') >= 100 ? 'completed' : 'in_progress',
            ]);
        }

        return $this->sendResponse($progress, 'Activity progress recorded successfully', 201);
    }

    /**
     * Add an admin followup note to an activity.
     */
    public function addFollowup(Request $request, int $id): JsonResponse
    {
        $activity = ActivityPlan::find($id);

        if (!$activity) {
            return $this->sendError('Activity not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:user_accounts,id',
            'followup_note' => 'required|string',
            'status' => 'nullable|in:pending,in_review,resolved,escalated',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $followup = ActivityFollowup::create([
            'activity_plan_id' => $activity->id,
            'admin_id' => $request->input('admin_id'),
            'followup_note' => $request->input('followup_note'),
            'status' => $request->input('status', 'pending'),
        ]);

        return $this->sendResponse($followup->load('admin'), 'Followup note added successfully', 201);
    }
}

