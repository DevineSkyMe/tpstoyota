<?php

namespace App\Http\Controllers;

use App\Models\ActivityPlan;
use App\Models\Employee;
use App\Models\Workshop;
use App\Models\WorkshopEvaluation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the role-aware TPS Key Person Development dashboard.
     */
    public function index(): View
    {
        $currentUser = auth()->user();
        $employee = $currentUser?->employee;

        // 1. Core summary metrics. Karyawan only sees their own numbers.
        $activityQuery = ActivityPlan::query();

        if ($currentUser?->isKaryawan() && $employee) {
            $activityQuery->where('employee_id', $employee->id);
        }

        $kaizenProjects = (clone $activityQuery)->count();
        $underReview = (clone $activityQuery)->whereIn('status', ['in_progress', 'delay'])->count();
        $completedActivities = (clone $activityQuery)->where('status', 'completed')->count();

        $totalAgents = $currentUser?->isKaryawan()
            ? ($employee ? 1 : 0)
            : Employee::where('status', 'active')->count();

        // 2. Success rate from real evaluations.
        $totalEvaluated = WorkshopEvaluation::count();
        $passedCount = WorkshopEvaluation::where('final_decision', 'pass')->count();
        $successRate = $totalEvaluated > 0 ? round(($passedCount / $totalEvaluated) * 100, 1) : 0;

        // 3. TPS level metrics.
        $kp4Count = Employee::where('current_tps_level', 'KP4')->count();
        $kp3Count = Employee::where('current_tps_level', 'KP3')->count();
        $advanceCount = Employee::whereIn('current_tps_level', ['Advance', 'KP2', 'KP1'])->count();

        // 4. Active workshop / evaluation window.
        $activeWorkshop = Workshop::whereIn('status', ['scheduled', 'ongoing'])
            ->orderBy('start_date')
            ->first()
            ?? Workshop::latest('start_date')->first();

        $evaluationWindow = [
            'start' => $activeWorkshop?->start_date?->format('M d'),
            'end' => $activeWorkshop?->end_date?->format('M d, Y'),
            'formatted' => $activeWorkshop
                ? $activeWorkshop->start_date->format('M d') . ' – ' . $activeWorkshop->end_date->format('M d, Y')
                : 'Belum ada jadwal workshop',
        ];

        // 5. Feature cards are generated per role so every icon is clickable and permitted.
        $cards = $this->cardsFor($currentUser, [
            'kaizenProjects' => $kaizenProjects,
            'completedActivities' => $completedActivities,
            'kp4Count' => $kp4Count,
            'kp3Count' => $kp3Count,
            'advanceCount' => $advanceCount,
            'employee' => $employee,
            'evaluationWindow' => $evaluationWindow,
        ]);

        return view('dashboard', compact(
            'currentUser',
            'totalAgents',
            'kaizenProjects',
            'underReview',
            'completedActivities',
            'successRate',
            'kp4Count',
            'kp3Count',
            'advanceCount',
            'activeWorkshop',
            'evaluationWindow',
            'cards'
        ));
    }

    /**
     * Build the dashboard feature cards for the authenticated role.
     */
    private function cardsFor(?object $user, array $stats): array
    {
        $base = [];

        // Activity & Workshop are not part of the HRD workspace.
        if (! $user?->isHrd()) {
            $base[] = [
                'title' => 'Activity',
                'subtitle' => $user?->isKaryawan() ? 'Buat & pantau aktivitas Kaizen Anda' : 'Project logs & monitoring',
                'badge' => 'Jishuken Logs',
                'href' => route('activity'),
                'gradient' => 'from-[#ec4899] via-[#db2777] to-[#be185d]',
                'shadow' => 'shadow-pink-500/15',
                'icon' => 'clipboard',
                'stat' => $stats['kaizenProjects'] . ' projects',
            ];

            $base[] = [
                'title' => 'Workshop',
                'subtitle' => $user?->isKaryawan() ? 'Lihat & ikuti workshop TPS' : 'Kelola batch & peserta workshop',
                'badge' => 'Workshop',
                'href' => route('workshops.index'),
                'gradient' => 'from-[#10b981] via-[#059669] to-[#047857]',
                'shadow' => 'shadow-emerald-500/15',
                'icon' => 'users',
                'stat' => Workshop::count() . ' batch',
            ];
        }

        if ($user?->isKaryawan()) {
            $base[] = [
                'title' => 'Profil Saya',
                'subtitle' => 'Riwayat level TPS, aktivitas, dan hasil workshop',
                'badge' => 'My Profile',
                'href' => route('profile'),
                'gradient' => 'from-[#3b82f6] via-[#2563eb] to-[#1d4ed8]',
                'shadow' => 'shadow-blue-500/15',
                'icon' => 'profile',
                'stat' => $stats['employee']?->current_tps_level ?? 'Belum terhubung',
            ];

            return $base;
        }

        // Directories & analytics: Admin, OMDD, Manager (not HRD).
        if ($user?->hasRole('admin', 'omdd', 'manager')) {
            $base[] = [
                'title' => 'Dashboard Analytics',
                'subtitle' => 'Executive metrics & analytics',
                'badge' => 'Analytics Hub',
                'href' => route('analytics'),
                'gradient' => 'from-[#00b4d8] via-[#0096c7] to-[#0077b6]',
                'shadow' => 'shadow-cyan-500/15',
                'icon' => 'chart',
                'stat' => 'FY ' . date('Y'),
            ];

            $base[] = [
                'title' => 'TPS KP 4',
                'subtitle' => 'Master key persons directory',
                'badge' => 'Level 4 Elite',
                'href' => route('tps.kp4'),
                'gradient' => 'from-[#f59e0b] via-[#eab308] to-[#d97706]',
                'shadow' => 'shadow-amber-500/15',
                'icon' => 'medal',
                'stat' => $stats['kp4Count'] . ' key persons',
            ];

            $base[] = [
                'title' => 'TPS KP 3',
                'subtitle' => 'Senior practitioner board',
                'badge' => 'Level 3 Senior',
                'href' => route('tps.kp3'),
                'gradient' => 'from-[#3b82f6] via-[#2563eb] to-[#1d4ed8]',
                'shadow' => 'shadow-blue-500/15',
                'icon' => 'shield',
                'stat' => $stats['kp3Count'] . ' key persons',
            ];

            $base[] = [
                'title' => 'TPS Advance',
                'subtitle' => 'Continuous kaizen pipeline',
                'badge' => 'Acceleration',
                'href' => route('tps.advance'),
                'gradient' => 'from-[#8b5cf6] via-[#7c3aed] to-[#6d28d9]',
                'shadow' => 'shadow-purple-500/15',
                'icon' => 'rocket',
                'stat' => $stats['advanceCount'] . ' pipeline',
            ];
        }

        if ($user?->hasRole('admin', 'hrd', 'omdd', 'manager')) {
            $base[] = [
                'title' => 'Next Candidate',
                'subtitle' => $user?->isHrd()
                    ? 'Berikan rekomendasi peserta workshop berikutnya'
                    : 'OMDD evaluation & candidate pipeline',
                'badge' => $user?->isHrd() ? 'Rekomendasi HRD' : 'Assessment',
                'href' => route('candidates.index'),
                'gradient' => 'from-[#10b981] via-[#059669] to-[#047857]',
                'shadow' => 'shadow-emerald-500/15',
                'icon' => 'userSearch',
                'stat' => 'Pipeline aktif',
            ];
        }

        if ($user?->hasRole('admin', 'manager', 'hrd')) {
            $base[] = [
                'title' => 'Laporan',
                'subtitle' => 'Laporan activity & hasil workshop',
                'badge' => 'Reports',
                'href' => route('reports.index'),
                'gradient' => 'from-slate-700 via-slate-800 to-slate-900',
                'shadow' => 'shadow-slate-500/15',
                'icon' => 'report',
                'stat' => $user?->isHrd() ? 'Dari Admin' : 'Siap dikirim',
            ];
        }

        return $base;
    }
}
