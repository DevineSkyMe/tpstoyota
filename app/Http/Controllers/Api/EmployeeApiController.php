<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeApiController extends BaseApiController
{
    /**
     * Display a listing of employees with filters and pagination.
     */
    public function index(Request $request)
    {
        if ($request->acceptsHtml() && ! $request->expectsJson()) {
            return redirect()->route('employees.index', $request->query());
        }

        $query = Employee::with(['plant', 'division', 'department', 'userAccount']);

        // Search by name or username (NIK)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by Plant
        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->input('plant_id'));
        }

        // Filter by Division
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->input('division_id'));
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Filter by TPS Level
        if ($request->filled('tps_level')) {
            $query->where('current_tps_level', $request->input('tps_level'));
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = $request->integer('per_page', 15);
        $employees = $query->latest()->paginate($perPage);

        return $this->sendResponse($employees, 'Employees retrieved successfully');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:20|unique:employees,username',
            'birth_date' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'current_tps_level' => 'nullable|string|max:50',
            'previous_tps_level' => 'nullable|string|max:50',
            'tahun_lulus_terakhir' => 'nullable|string|max:20',
            'tahun_lulus_saat_ini' => 'nullable|string|max:20',
            'last_activity_theme' => 'nullable|string|max:200',
            'last_activity_type' => 'nullable|string|max:50',
            'batch' => 'nullable|string|max:50',
            'registration_year' => 'nullable|integer',
            'status' => 'nullable|string|max:20',
            'plant_id' => 'nullable|exists:plants,id',
            'division_id' => 'nullable|exists:divisions,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $employee = Employee::create($request->all());

        return $this->sendResponse($employee->load(['plant', 'division', 'department']), 'Employee created successfully', 201);
    }

    /**
     * Display the specified employee.
     */
    public function show(int $id)
    {
        if (request()->acceptsHtml() && ! request()->expectsJson()) {
            return redirect()->route('employees.show', $id);
        }

        $employee = Employee::with([
            'plant',
            'division',
            'department',
            'userAccount',
            'activityPlans',
            'workshopCandidates',
            'workshopEvaluations'
        ])->find($id);

        if (!$employee) {
            return $this->sendError('Employee not found', [], 404);
        }

        return $this->sendResponse($employee, 'Employee retrieved successfully');
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return $this->sendError('Employee not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'username' => 'sometimes|required|string|max:20|unique:employees,username,' . $id,
            'birth_date' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'current_tps_level' => 'nullable|string|max:50',
            'previous_tps_level' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:20',
            'plant_id' => 'nullable|exists:plants,id',
            'division_id' => 'nullable|exists:divisions,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $employee->update($request->all());

        return $this->sendResponse($employee->fresh(['plant', 'division', 'department']), 'Employee updated successfully');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(int $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return $this->sendError('Employee not found', [], 404);
        }

        $employee->delete();

        return $this->sendResponse(null, 'Employee deleted successfully');
    }
}

