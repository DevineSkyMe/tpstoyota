<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Plant;
use App\Models\UserAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees with search and multi-criteria filters.
     */
    public function index(Request $request): View
    {
        $query = Employee::with(['plant', 'department', 'division']);

        // Search by keyword (Name, NIK, Position)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Filter by Plant
        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->input('plant_id'));
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Filter by TPS Level
        if ($request->filled('level')) {
            $query->where('current_tps_level', $request->input('level'));
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = (int) $request->input('rows', 10);
        $employees = $query->orderBy('name', 'asc')->paginate($perPage)->withQueryString();

        // Dropdown options
        $plants = Plant::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        // Summary Counters
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $kp4Count = Employee::where('current_tps_level', 'KP4')->count();
        $kp3Count = Employee::where('current_tps_level', 'KP3')->count();
        $advanceCount = Employee::whereIn('current_tps_level', ['Advance', 'KP2', 'KP1'])->count();

        return view('employees.index', compact(
            'employees',
            'plants',
            'departments',
            'divisions',
            'totalEmployees',
            'activeEmployees',
            'kp4Count',
            'kp3Count',
            'advanceCount'
        ));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $plants = Plant::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('employees.create', compact('plants', 'divisions', 'departments'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:20|unique:employees,username',
            'birth_date' => 'nullable|date',
            'position' => 'required|string|max:100',
            'plant_id' => 'required|exists:plants,id',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'current_tps_level' => 'required|string|max:20',
            'previous_tps_level' => 'nullable|string|max:20',
            'tahun_lulus_terakhir' => 'nullable|digits:4',
            'tahun_lulus_saat_ini' => 'nullable|digits:4',
            'last_activity_theme' => 'nullable|string|max:200',
            'last_activity_type' => 'nullable|string|max:50',
            'batch' => 'nullable|integer',
            'registration_year' => 'nullable|integer',
            'status' => 'required|string|in:active,inactive',
        ]);

        $employee = Employee::create($validated);

        // Every new employee automatically gets a karyawan login account.
        UserAccount::firstOrCreate(
            ['employee_id' => $employee->id],
            [
                'username' => $this->uniqueEmployeeUsername($employee),
                'email' => strtolower($employee->username) . '@tps.local',
                'password' => Hash::make('password'),
                'role' => UserAccount::ROLE_KARYAWAN,
            ]
        );

        return redirect()->route('employees.show', $employee->id)
            ->with('success', "Pegawai {$employee->name} (NIK: {$employee->username}) berhasil ditambahkan. Akun login karyawan dibuat dengan password awal: password.");
    }

    /**
     * Display the specified employee profile with full dossier.
     */
    public function show(int $id): View
    {
        $employee = Employee::with([
            'plant',
            'division',
            'department',
            'activityPlans' => fn ($q) => $q->orderBy('created_at', 'desc'),
            'workshopCandidates.hrdReview',
            'workshopParticipants.workshop',
            'workshopEvaluations.workshop',
        ])->findOrFail($id);

        // Activity metrics
        $totalActivities = $employee->activityPlans->count();
        $completedActivities = $employee->activityPlans->where('status', 'completed')->count();
        $inProgressActivities = $employee->activityPlans->where('status', 'in_progress')->count();

        // Latest Evaluation
        $latestEvaluation = $employee->workshopEvaluations->sortByDesc('created_at')->first();

        // Spider/radar chart data: one polygon per workshop batch.
        $radarChart = $employee->radarChartData();

        return view('employees.show', compact(
            'employee',
            'totalActivities',
            'completedActivities',
            'inProgressActivities',
            'latestEvaluation',
            'radarChart'
        ));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(int $id): View
    {
        $employee = Employee::findOrFail($id);
        $plants = Plant::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('employees.edit', compact('employee', 'plants', 'divisions', 'departments'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => "required|string|max:20|unique:employees,username,{$employee->id}",
            'birth_date' => 'nullable|date',
            'position' => 'required|string|max:100',
            'plant_id' => 'required|exists:plants,id',
            'division_id' => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'current_tps_level' => 'required|string|max:20',
            'previous_tps_level' => 'nullable|string|max:20',
            'tahun_lulus_terakhir' => 'nullable|digits:4',
            'tahun_lulus_saat_ini' => 'nullable|digits:4',
            'last_activity_theme' => 'nullable|string|max:200',
            'last_activity_type' => 'nullable|string|max:50',
            'batch' => 'nullable|integer',
            'registration_year' => 'nullable|integer',
            'status' => 'required|string|in:active,inactive',
        ]);

        $employee->update($validated);

        // Keep a login account available for every employee profile.
        UserAccount::firstOrCreate(
            ['employee_id' => $employee->id],
            [
                'username' => $this->uniqueEmployeeUsername($employee),
                'email' => strtolower($employee->username) . '@tps.local',
                'password' => Hash::make('password'),
                'role' => UserAccount::ROLE_KARYAWAN,
            ]
        );

        return redirect()->route('employees.show', $employee->id)
            ->with('success', "Data pegawai {$employee->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $name = $employee->name;

        // Remove the employee's login account so no orphaned accounts remain.
        UserAccount::where('employee_id', $employee->id)->delete();

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', "Pegawai {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Build a unique login username for an employee account.
     */
    private function uniqueEmployeeUsername(Employee $employee): string
    {
        $base = 'tps_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $employee->username));
        $username = $base;
        $suffix = 1;

        while (UserAccount::where('username', $username)->exists()) {
            $username = $base . '_' . $suffix++;
        }

        return $username;
    }
}

