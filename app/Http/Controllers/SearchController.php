<?php

namespace App\Http\Controllers;

use App\Models\ActivityPlan;
use App\Models\Employee;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Global search across the records the current role is allowed to see.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $term = trim((string) $request->input('q', ''));

        $employees = collect();
        $activities = collect();
        $workshops = collect();

        // HRD may not browse employees, activity logs or workshops.
        if ($term !== '' && ! $user->isHrd()) {
            $employeeQuery = Employee::with(['plant', 'department']);

            if ($user->isKaryawan()) {
                $employeeQuery->where('id', $user->employee_id ?? 0);
            }

            $employees = $employeeQuery
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%")
                        ->orWhere('position', 'like', "%{$term}%");
                })
                ->limit(8)
                ->get();

            $activityQuery = ActivityPlan::with('employee')->latest();

            if ($user->isKaryawan()) {
                $activityQuery->where('employee_id', $user->employee_id ?? 0);
            }

            $activities = $activityQuery
                ->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('pic', 'like', "%{$term}%");
                })
                ->limit(8)
                ->get();

            $workshops = Workshop::where('title', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->limit(8)
                ->get();
        }

        return view('search.index', compact('term', 'employees', 'activities', 'workshops'));
    }
}
