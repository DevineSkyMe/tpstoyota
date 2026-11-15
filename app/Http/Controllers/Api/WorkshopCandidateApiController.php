<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkshopCandidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkshopCandidateApiController extends BaseApiController
{
    /**
     * Stage 1 (TLC): Display a listing of workshop candidates.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorkshopCandidate::with(['employee.department', 'employee.plant', 'recommendedBy', 'hrdReview']);

        if ($request->filled('status')) {
            $query->where('tlc_status', $request->input('status'));
        }

        if ($request->filled('target_level')) {
            $query->where('target_tps_level', $request->input('target_level'));
        }

        $candidates = $query->latest()->paginate($request->integer('per_page', 15));

        return $this->sendResponse($candidates, 'Workshop candidates retrieved successfully');
    }

    /**
     * Stage 1 (TLC): Nominate an employee as a candidate.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'recommended_by' => 'required|exists:user_accounts,id',
            'target_tps_level' => 'nullable|string|max:50',
            'tlc_reason' => 'nullable|string',
            'tlc_status' => 'nullable|in:pending,recommended,rejected',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $data = $request->all();
        if (($data['tlc_status'] ?? 'pending') !== 'pending') {
            $data['tlc_reviewed_at'] = now();
        }

        $candidate = WorkshopCandidate::create($data);

        return $this->sendResponse($candidate->load(['employee', 'recommendedBy']), 'Candidate nominated successfully', 201);
    }

    /**
     * Stage 1 (TLC): Review or update candidate status.
     */
    public function review(Request $request, int $id): JsonResponse
    {
        $candidate = WorkshopCandidate::find($id);

        if (!$candidate) {
            return $this->sendError('Candidate not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'tlc_status' => 'required|in:pending,recommended,rejected',
            'tlc_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $candidate->update([
            'tlc_status' => $request->input('tlc_status'),
            'tlc_reason' => $request->input('tlc_reason', $candidate->tlc_reason),
            'tlc_reviewed_at' => now(),
        ]);

        return $this->sendResponse($candidate->fresh(['employee', 'recommendedBy']), 'Candidate recommendation updated successfully');
    }
}

