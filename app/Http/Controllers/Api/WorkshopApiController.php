<?php

namespace App\Http\Controllers\Api;

use App\Models\Workshop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkshopApiController extends BaseApiController
{
    /**
     * Display a listing of workshops.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Workshop::with(['creator'])->withCount(['participants', 'evaluations']);

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $workshops = $query->latest('start_date')->paginate($request->integer('per_page', 10));

        return $this->sendResponse($workshops, 'Workshops retrieved successfully');
    }

    /**
     * Store a newly created workshop.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer',
            'batch' => 'required|integer',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:150',
            'quota' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,scheduled,ongoing,completed,cancelled',
            'created_by' => 'nullable|exists:user_accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $workshop = Workshop::create($request->all());

        return $this->sendResponse($workshop, 'Workshop created successfully', 201);
    }

    /**
     * Display the specified workshop with participants and evaluations.
     */
    public function show(int $id): JsonResponse
    {
        $workshop = Workshop::with([
            'creator',
            'participants.employee.department',
            'evaluations.employee',
            'scoreReports.generator'
        ])->find($id);

        if (!$workshop) {
            return $this->sendError('Workshop not found', [], 404);
        }

        return $this->sendResponse($workshop, 'Workshop details retrieved successfully');
    }

    /**
     * Update the specified workshop.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $workshop = Workshop::find($id);

        if (!$workshop) {
            return $this->sendError('Workshop not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:150',
            'quota' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,scheduled,ongoing,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $workshop->update($request->all());

        return $this->sendResponse($workshop->fresh(), 'Workshop updated successfully');
    }

    /**
     * Remove the specified workshop.
     */
    public function destroy(int $id): JsonResponse
    {
        $workshop = Workshop::find($id);

        if (!$workshop) {
            return $this->sendError('Workshop not found', [], 404);
        }

        $workshop->delete();

        return $this->sendResponse(null, 'Workshop deleted successfully');
    }
}

