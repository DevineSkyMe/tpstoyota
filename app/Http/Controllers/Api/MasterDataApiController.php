<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Models\Division;
use App\Models\Plant;
use Illuminate\Http\JsonResponse;

class MasterDataApiController extends BaseApiController
{
    /**
     * Get all master data (plants, divisions, departments) in a single response for frontend dropdowns.
     */
    public function all(): JsonResponse
    {
        $data = [
            'plants' => Plant::all(),
            'divisions' => Division::all(),
            'departments' => Department::all(),
            'tps_levels' => ['KP1', 'KP2', 'KP3', 'KP4', 'Advance'],
            'activity_categories' => [
                'Logistics 4.0',
                'Standardized Work',
                'Smart Genba AI',
                'Zero Defect (Jidoka)',
                'Low-Cost Automation',
                'Just-In-Time (JIT)',
                'Safety & Ergonomics',
                'Kaizen Project',
                'QCC',
                'Jishuken',
            ],
            'roles' => ['admin', 'tlc', 'hrd', 'omdd', 'director', 'participant', 'tpsg'],
        ];

        return $this->sendResponse($data, 'Master data retrieved successfully');
    }

    public function plants(): JsonResponse
    {
        return $this->sendResponse(Plant::all(), 'Plants retrieved successfully');
    }

    public function divisions(): JsonResponse
    {
        return $this->sendResponse(Division::all(), 'Divisions retrieved successfully');
    }

    public function departments(): JsonResponse
    {
        return $this->sendResponse(Department::all(), 'Departments retrieved successfully');
    }
}

