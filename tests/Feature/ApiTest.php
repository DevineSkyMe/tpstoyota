<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * Test Dashboard Stats API.
     */
    public function test_dashboard_stats_api_returns_success(): void
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'metrics',
                    'system_status',
                    'tps_distribution',
                ],
            ]);
    }

    /**
     * Test Master Data API.
     */
    public function test_master_data_all_api_returns_success(): void
    {
        $response = $this->getJson('/api/master/all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'plants',
                    'divisions',
                    'departments',
                    'tps_levels',
                ],
            ]);
    }

    /**
     * Test Employees List API.
     */
    public function test_employees_list_api_returns_success(): void
    {
        $response = $this->getJson('/api/employees');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test Activities List API.
     */
    public function test_activities_list_api_returns_success(): void
    {
        $response = $this->getJson('/api/activities');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test Workshops List API.
     */
    public function test_workshops_list_api_returns_success(): void
    {
        $response = $this->getJson('/api/workshops');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test Workshop Stage 1 (TLC Candidates) API.
     */
    public function test_workshop_candidates_api(): void
    {
        $response = $this->getJson('/api/workshop-candidates');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test Workshop Stage 4 (OMDD Evaluations) API.
     */
    public function test_workshop_evaluations_api(): void
    {
        $response = $this->getJson('/api/workshop-evaluations');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test Messages API.
     */
    public function test_messages_api(): void
    {
        $response = $this->getJson('/api/messages');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}

