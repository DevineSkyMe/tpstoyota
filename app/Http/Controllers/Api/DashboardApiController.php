<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityPlan;
use App\Models\BatchStat;
use App\Models\Employee;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopEvaluation;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends BaseApiController
{
    /**
     * Get dashboard summary metrics & KPI statistics.
     */
    public function stats(): JsonResponse
    {
        $totalAgents = Employee::where('status', 'active')->count();
        $kaizenProjects = ActivityPlan::count();
        $underReview = ActivityPlan::whereIn('status', ['in_progress', 'delay'])->count();
        
        $totalEvaluated = WorkshopEvaluation::count();
        $passedCount = WorkshopEvaluation::where('final_decision', 'PASS')->count();
        $successRate = $totalEvaluated > 0 ? round(($passedCount / $totalEvaluated) * 100, 1) : 0;

        $tpsDistribution = [
            'kp4' => Employee::where('current_tps_level', 'KP4')->count(),
            'kp3' => Employee::where('current_tps_level', 'KP3')->count(),
            'kp2' => Employee::where('current_tps_level', 'KP2')->count(),
            'kp1' => Employee::where('current_tps_level', 'KP1')->count(),
        ];

        $latestWorkshop = Workshop::latest('start_date')->first();
        $batchStats = BatchStat::latest()->take(5)->get();

        $data = [
            'metrics' => [
                'total_agents' => $totalAgents,
                'kaizen_projects' => $kaizenProjects,
                'under_review' => $underReview,
                'success_rate' => $successRate . '%',
                'pass_count' => $passedCount,
                'total_evaluated' => $totalEvaluated,
            ],
            'system_status' => [
                'engine' => 'OMDD Engine Synced',
                'cadence_period' => 'FY2025 Q1 Jishuken',
                'evaluation_window' => [
                    'start' => '2025-10-24',
                    'end' => '2025-11-12',
                    'formatted' => 'Oct 24 – Nov 12, 2025',
                ],
            ],
            'tps_distribution' => $tpsDistribution,
            'active_workshop' => $latestWorkshop,
            'batch_stats' => $batchStats,
        ];

        return $this->sendResponse($data, 'Dashboard statistics retrieved successfully');
    }
}

