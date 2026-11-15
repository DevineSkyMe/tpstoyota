<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkshopParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkshopParticipantApiController extends BaseApiController
{
    /**
     * Stage 3 (Admin): List confirmed workshop participants.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorkshopParticipant::with(['workshop', 'employee.department', 'candidate', 'confirmedBy']);

        if ($request->filled('workshop_id')) {
            $query->where('workshop_id', $request->input('workshop_id'));
        }

        if ($request->filled('status')) {
            $query->where('confirmation_status', $request->input('status'));
        }

        $participants = $query->paginate($request->integer('per_page', 20));

        return $this->sendResponse($participants, 'Workshop participants retrieved successfully');
    }

    /**
     * Stage 3 (Admin): Confirm participant into a workshop.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'workshop_id' => 'required|exists:workshops,id',
            'employee_id' => 'required|exists:employees,id',
            'candidate_id' => 'nullable|exists:workshop_candidates,id',
            'confirmed_by' => 'required|exists:user_accounts,id',
            'confirmation_status' => 'nullable|in:confirmed,waitlist,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        // Prevent duplicate workshop_id + employee_id
        $exists = WorkshopParticipant::where('workshop_id', $request->input('workshop_id'))
            ->where('employee_id', $request->input('employee_id'))
            ->exists();

        if ($exists) {
            return $this->sendError('Employee is already enrolled in this workshop', [], 409);
        }

        $participant = WorkshopParticipant::create($request->all());

        return $this->sendResponse($participant->load(['workshop', 'employee']), 'Participant confirmed successfully', 201);
    }

    /**
     * Stage 3 (Admin): Update participant confirmation status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $participant = WorkshopParticipant::find($id);

        if (!$participant) {
            return $this->sendError('Participant record not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'confirmation_status' => 'required|in:confirmed,waitlist,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $participant->update([
            'confirmation_status' => $request->input('confirmation_status')
        ]);

        return $this->sendResponse($participant, 'Participant status updated successfully');
    }

    /**
     * Remove participant from workshop.
     */
    public function destroy(int $id): JsonResponse
    {
        $participant = WorkshopParticipant::find($id);

        if (!$participant) {
            return $this->sendError('Participant record not found', [], 404);
        }

        $participant->delete();

        return $this->sendResponse(null, 'Participant removed successfully');
    }
}

