<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkshopEvaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkshopEvaluationApiController extends BaseApiController
{
    /**
     * Stage 4 (OMDD): List workshop evaluations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorkshopEvaluation::with(['employee.department', 'workshop', 'participant', 'evaluator']);

        if ($request->filled('workshop_id')) {
            $query->where('workshop_id', $request->input('workshop_id'));
        }

        if ($request->filled('decision')) {
            $query->where('final_decision', $request->input('decision'));
        }

        $evaluations = $query->latest('evaluated_at')->paginate($request->integer('per_page', 20));

        return $this->sendResponse($evaluations, 'Evaluations retrieved successfully');
    }

    /**
     * Stage 4 (OMDD): Record scores ws_1 ... ws_7 and evaluate a participant.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'workshop_id' => 'nullable|exists:workshops,id',
            'participant_id' => 'nullable|exists:workshop_participants,id',
            'ws_1' => 'nullable|integer|min:0|max:100',
            'ws_2' => 'nullable|integer|min:0|max:100',
            'ws_3' => 'nullable|integer|min:0|max:100',
            'ws_4' => 'nullable|integer|min:0|max:100',
            'ws_5' => 'nullable|integer|min:0|max:100',
            'ws_6' => 'nullable|integer|min:0|max:100',
            'ws_7' => 'nullable|integer|min:0|max:100',
            'final_decision' => 'nullable|in:PASS,FAIL',
            'notes' => 'nullable|string',
            'evaluated_by' => 'nullable|string|max:100',
            'evaluated_by_id' => 'nullable|exists:user_accounts,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $data = $request->all();

        // Automatically calculate decision if not provided (threshold average >= 75)
        if (!isset($data['final_decision'])) {
            $scores = [
                $data['ws_1'] ?? 0,
                $data['ws_2'] ?? 0,
                $data['ws_3'] ?? 0,
                $data['ws_4'] ?? 0,
                $data['ws_5'] ?? 0,
                $data['ws_6'] ?? 0,
                $data['ws_7'] ?? 0,
            ];
            $avg = array_sum($scores) / count($scores);
            $data['final_decision'] = $avg >= 75 ? 'PASS' : 'FAIL';
        }

        $data['evaluated_at'] = now();

        $evaluation = WorkshopEvaluation::create($data);

        return $this->sendResponse($evaluation->load(['employee', 'workshop']), 'Evaluation saved successfully', 201);
    }

    /**
     * Stage 4 (OMDD): Display single evaluation.
     */
    public function show(int $id): JsonResponse
    {
        $evaluation = WorkshopEvaluation::with(['employee', 'workshop', 'evaluator'])->find($id);

        if (!$evaluation) {
            return $this->sendError('Evaluation not found', [], 404);
        }

        return $this->sendResponse($evaluation, 'Evaluation details retrieved successfully');
    }

    /**
     * Stage 4 (OMDD): Update evaluation scores.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $evaluation = WorkshopEvaluation::find($id);

        if (!$evaluation) {
            return $this->sendError('Evaluation not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'ws_1' => 'nullable|integer|min:0|max:100',
            'ws_2' => 'nullable|integer|min:0|max:100',
            'ws_3' => 'nullable|integer|min:0|max:100',
            'ws_4' => 'nullable|integer|min:0|max:100',
            'ws_5' => 'nullable|integer|min:0|max:100',
            'ws_6' => 'nullable|integer|min:0|max:100',
            'ws_7' => 'nullable|integer|min:0|max:100',
            'final_decision' => 'nullable|in:PASS,FAIL',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $evaluation->update($request->all());

        return $this->sendResponse($evaluation->fresh(['employee']), 'Evaluation updated successfully');
    }
}

