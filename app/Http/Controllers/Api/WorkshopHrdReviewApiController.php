<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkshopHrdReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkshopHrdReviewApiController extends BaseApiController
{
    /**
     * Stage 2 (HRD): List HRD candidate reviews.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorkshopHrdReview::with(['candidate.employee', 'hrd']);

        if ($request->filled('status')) {
            $query->where('hrd_status', $request->input('status'));
        }

        $reviews = $query->latest()->paginate($request->integer('per_page', 15));

        return $this->sendResponse($reviews, 'HRD reviews retrieved successfully');
    }

    /**
     * Stage 2 (HRD): Submit an HRD review decision for a candidate.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'candidate_id' => 'required|exists:workshop_candidates,id',
            'hrd_id' => 'required|exists:user_accounts,id',
            'hrd_status' => 'required|in:pending,recommended,rejected',
            'hrd_note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $review = WorkshopHrdReview::updateOrCreate(
            ['candidate_id' => $request->input('candidate_id')],
            [
                'hrd_id' => $request->input('hrd_id'),
                'hrd_status' => $request->input('hrd_status'),
                'hrd_note' => $request->input('hrd_note'),
                'reviewed_at' => now(),
            ]
        );

        return $this->sendResponse($review->load(['candidate.employee', 'hrd']), 'HRD review submitted successfully', 201);
    }
}

