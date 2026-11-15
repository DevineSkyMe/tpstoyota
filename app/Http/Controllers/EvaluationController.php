<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopEvaluation;
use App\Models\WorkshopParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    /**
     * OMDD workspace: every confirmed participant and their evaluation status.
     */
    public function index(Request $request): View
    {
        $workshops = Workshop::orderByDesc('year')->orderByDesc('batch')->get();

        $query = WorkshopParticipant::with([
            'employee.plant',
            'employee.department',
            'workshop',
            'evaluation',
        ])->where('confirmation_status', 'confirmed');

        if ($request->filled('workshop_id')) {
            $query->where('workshop_id', $request->integer('workshop_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'pending') {
                $query->whereDoesntHave('evaluation');
            } else {
                $query->whereHas('evaluation', fn ($q) => $q->where('final_decision', $request->input('status')));
            }
        }

        $participants = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => WorkshopParticipant::where('confirmation_status', 'confirmed')->count(),
            'evaluated' => WorkshopEvaluation::count(),
            'passed' => WorkshopEvaluation::where('final_decision', 'pass')->count(),
            'failed' => WorkshopEvaluation::where('final_decision', 'fail')->count(),
        ];

        return view('evaluations.index', compact('participants', 'workshops', 'stats'));
    }

    /**
     * Show the OMDD rubric for one participant.
     */
    public function show(WorkshopParticipant $participant): View
    {
        $participant->load(['employee.plant', 'employee.department', 'workshop', 'evaluation', 'candidate']);

        $evaluation = $participant->evaluation;
        $benchmark = WorkshopEvaluation::PASSING_BENCHMARK;

        return view('evaluations.show', compact('participant', 'evaluation', 'benchmark'));
    }

    /**
     * Persist OMDD rubric scores and the pass/fail decision.
     */
    public function store(Request $request, WorkshopParticipant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'ws_1' => ['required', 'integer', 'min:1', 'max:5'],
            'ws_2' => ['required', 'integer', 'min:1', 'max:5'],
            'ws_3' => ['required', 'integer', 'min:1', 'max:5'],
            'ws_4' => ['required', 'integer', 'min:1', 'max:5'],
            'ws_5' => ['required', 'integer', 'min:1', 'max:5'],
            'ws_6' => ['required', 'integer', 'min:1', 'max:5'],
            'ws_7' => ['required', 'integer', 'min:1', 'max:5'],
            'final_decision' => ['required', 'in:pass,fail'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $evaluation = WorkshopEvaluation::updateOrCreate(
            [
                'employee_id' => $participant->employee_id,
                'workshop_id' => $participant->workshop_id,
            ],
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
            $participant->employee->promoteToNextLevel();

            if ($participant->candidate) {
                $participant->candidate->update(['tlc_status' => 'approved']);
            }
        }

        return redirect()->route('evaluations.index')
            ->with('success', "Nilai {$participant->employee->name} tersimpan: {$evaluation->composite_score}% — {$evaluation->result_label}.");
    }
}
