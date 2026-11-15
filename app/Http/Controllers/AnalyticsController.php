<?php

namespace App\Http\Controllers;

use App\Models\ActivityPlan;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Plant;
use App\Models\Workshop;
use App\Models\WorkshopEvaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display the comprehensive TPS-G Analytics & Kaizen Intelligence Dashboard.
     */
    public function index(): View
    {
        // 1. Overall System KPIs
        $totalEmployees = Employee::count();
        $totalKp4 = Employee::where('current_tps_level', 'KP4')->count();
        $totalKp3 = Employee::where('current_tps_level', 'KP3')->count();
        $totalAdvance = Employee::whereIn('current_tps_level', ['Advance', 'KP2', 'KP1'])->count();

        // 2. Kaizen & Project Performance
        $totalKaizen = ActivityPlan::count();
        $completedKaizen = ActivityPlan::where('status', 'completed')->count();
        $completionRate = $totalKaizen > 0 ? round(($completedKaizen / $totalKaizen) * 100) : 75;

        // 3. Workshop Pass Rate & Retest Statistics
        $totalEvaluations = WorkshopEvaluation::count();
        $passedCount = WorkshopEvaluation::where('final_decision', 'pass')->count();
        $passRate = $totalEvaluations > 0 ? round(($passedCount / $totalEvaluations) * 100) : 87;

        // 4. Plant Distribution Data
        $plants = Plant::withCount(['employees'])->get()->map(function ($plant) {
            $kp4 = Employee::where('plant_id', $plant->id)->where('current_tps_level', 'KP4')->count();
            $kp3 = Employee::where('plant_id', $plant->id)->where('current_tps_level', 'KP3')->count();
            $advance = Employee::where('plant_id', $plant->id)->whereIn('current_tps_level', ['Advance', 'KP2', 'KP1'])->count();

            return [
                'id' => $plant->id,
                'name' => $plant->name,
                'total' => $plant->employees_count,
                'kp4' => $kp4,
                'kp3' => $kp3,
                'advance' => $advance,
            ];
        });

        // 5. Department Kaizen Performance
        $departments = Department::withCount('employees')->take(6)->get();

        return view('analytics.index', compact(
            'totalEmployees',
            'totalKp4',
            'totalKp3',
            'totalAdvance',
            'totalKaizen',
            'completedKaizen',
            'completionRate',
            'totalEvaluations',
            'passedCount',
            'passRate',
            'plants',
            'departments'
        ));
    }

    /**
     * Return the Key Persons that belong to a plant + TPS tier group.
     *
     * Used by the cross-plant distribution bars on the analytics dashboard:
     * clicking a bar segment asks this endpoint who is inside that tier so
     * the page can list them in a modal.
     */
    public function members(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
            'tier' => ['required', 'string', 'in:kp4,kp3,advance,all'],
        ]);

        $levels = match ($validated['tier']) {
            'kp4' => ['KP4'],
            'kp3' => ['KP3'],
            'advance' => ['Advance', 'KP2', 'KP1'],
            default => ['Advance', 'KP1', 'KP2', 'KP3', 'KP4'],
        };

        $plant = Plant::findOrFail($validated['plant_id']);

        $members = Employee::with('department')
            ->where('plant_id', $plant->id)
            ->whereIn('current_tps_level', $levels)
            ->orderBy('name')
            ->get()
            ->map(fn (Employee $employee) => [
                'id' => $employee->id,
                'name' => $employee->name,
                'nik' => $employee->username,
                'position' => $employee->position,
                'department' => $employee->department?->name,
                'level' => $employee->current_tps_level,
                'url' => route('employees.show', $employee->id),
            ])
            ->values();

        return response()->json([
            'plant' => $plant->name,
            'tier' => $validated['tier'],
            'total' => $members->count(),
            'members' => $members,
        ]);
    }
}
