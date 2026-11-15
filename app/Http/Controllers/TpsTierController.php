<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TpsTierController extends Controller
{
    /**
     * Display TPS Key Person 4 (KP 4) specialists and dashboard.
     */
    public function kp4(Request $request): View
    {
        $query = Employee::with(['plant', 'department', 'activityPlans'])
            ->where('current_tps_level', 'KP4');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->input('plant_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $employees = $query->orderBy('name')->paginate(8)->withQueryString();
        $plants = Plant::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        // Metrics for KP 4
        $totalKp4 = Employee::where('current_tps_level', 'KP4')->count();
        $totalProjectsCoached = 28;
        $totalYieldSavings = '$840,000';
        $yokotenReplications = 14;

        return view('tps.kp4', compact(
            'employees',
            'plants',
            'departments',
            'totalKp4',
            'totalProjectsCoached',
            'totalYieldSavings',
            'yokotenReplications'
        ));
    }

    /**
     * Display TPS Key Person 3 (KP 3) implementers and pipeline dashboard.
     */
    public function kp3(Request $request): View
    {
        $query = Employee::with(['plant', 'department', 'activityPlans'])
            ->where('current_tps_level', 'KP3');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->input('plant_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $employees = $query->orderBy('name')->paginate(8)->withQueryString();
        $plants = Plant::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        // Metrics for KP 3
        $totalKp3 = Employee::where('current_tps_level', 'KP3')->count();
        $pipelineToKp4 = Employee::where('current_tps_level', 'KP3')->whereHas('workshopCandidates')->count();
        $activeGenbaKaizen = 19;
        $certificationPassRate = '92%';

        return view('tps.kp3', compact(
            'employees',
            'plants',
            'departments',
            'totalKp3',
            'pipelineToKp4',
            'activeGenbaKaizen',
            'certificationPassRate'
        ));
    }

    /**
     * Display TPS Advance (KP 1 & KP 2 / Entry & Intermediate) practitioners.
     */
    public function advance(Request $request): View
    {
        $query = Employee::with(['plant', 'department', 'activityPlans'])
            ->whereIn('current_tps_level', ['Advance', 'KP2', 'KP1']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->input('plant_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $employees = $query->orderBy('name')->paginate(8)->withQueryString();
        $plants = Plant::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        // Metrics for TPS Advance
        $totalAdvance = Employee::whereIn('current_tps_level', ['Advance', 'KP2', 'KP1'])->count();
        $readinessScore = '84%';
        $completed5SCourses = 32;
        $mentorshipPairs = 18;

        return view('tps.advance', compact(
            'employees',
            'plants',
            'departments',
            'totalAdvance',
            'readinessScore',
            'completed5SCourses',
            'mentorshipPairs'
        ));
    }
}

