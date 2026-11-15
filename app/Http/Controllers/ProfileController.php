<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the signed-in account profile. Karyawan see their full employee dossier.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $employee = $user->employee;

        if ($employee) {
            $employee->load([
                'plant',
                'division',
                'department',
                'activityPlans' => fn ($q) => $q->latest(),
                'workshopParticipants.workshop',
                'workshopParticipants.evaluation',
                'workshopCandidates.hrdReview',
                'workshopEvaluations.workshop',
            ]);
        }

        $activities = $employee?->activityPlans ?? collect();
        $participants = $employee?->workshopParticipants ?? collect();
        $evaluations = $employee?->workshopEvaluations ?? collect();
        $latestEvaluation = $evaluations->sortByDesc('evaluated_at')->first();

        $stats = [
            'activities' => $activities->count(),
            'completed' => $activities->where('status', 'completed')->count(),
            'workshops' => $participants->count(),
            'passed' => $evaluations->filter(fn ($evaluation) => $evaluation->isPassed())->count(),
        ];

        $unreadMessages = Message::where('receiver_id', $user->id)->where('is_read', false)->count();

        $radarChart = $employee
            ? $employee->radarChartData()
            : ['labels' => [], 'datasets' => []];

        return view('profile.show', compact(
            'user',
            'employee',
            'activities',
            'participants',
            'evaluations',
            'latestEvaluation',
            'stats',
            'unreadMessages',
            'radarChart'
        ));
    }

    /**
     * Change the signed-in account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
