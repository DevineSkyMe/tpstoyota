<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Plant;
use App\Models\UserAccount;
use App\Models\WorkshopCandidate;
use Tests\TestCase;

class PortalFeaturesTest extends TestCase
{
    private function loginAs(string $role): UserAccount
    {
        $user = UserAccount::where('role', $role)->orderBy('id')->firstOrFail();
        $this->actingAs($user);

        return $user;
    }

    public function test_employees_index_loads_with_filters_and_data(): void
    {
        $this->assertEquals(url('/employees'), route('employees.index'));
        $this->assertEquals(url('/employees/13'), route('employees.show', 13));
        $this->assertEquals(url('/api/employees/13'), route('api.employees.show', 13));

        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('employees.index'));
        $response->assertStatus(200);
        $response->assertSee('TPS Key Person & Employees', false);
        $response->assertSee('Tambah Pegawai Baru');
    }

    public function test_api_employees_browser_request_redirects_to_web_view(): void
    {
        $response = $this->get('/api/employees', ['Accept' => 'text/html']);
        $response->assertRedirect(route('employees.index'));

        $response2 = $this->get('/api/employees/13', ['Accept' => 'text/html']);
        $response2->assertRedirect(route('employees.show', 13));
    }

    public function test_employees_search_works(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('employees.index', ['search' => 'Kenji']));
        $response->assertStatus(200);
        $response->assertSee('Kenji Takahashi');
    }

    public function test_employees_create_page_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('employees.create'));
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Pegawai & TPS Key Person', false);
    }

    public function test_employee_can_be_created_updated_and_deleted(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $plant = Plant::first();
        $division = Division::first();
        $department = Department::first();

        // Unique username for testing
        $testNik = 'TEST' . rand(1000, 9999);

        // 1. Create
        $response = $this->post(route('employees.store'), [
            'name' => 'Budi Santoso TPS',
            'username' => $testNik,
            'birth_date' => '1992-03-10',
            'position' => 'Press Kaizen Specialist',
            'plant_id' => $plant->id,
            'division_id' => $division->id,
            'department_id' => $department->id,
            'current_tps_level' => 'KP3',
            'previous_tps_level' => 'Advance',
            'status' => 'active',
        ]);

        $createdEmp = Employee::where('username', $testNik)->first();
        $this->assertNotNull($createdEmp);
        $response->assertRedirect(route('employees.show', $createdEmp->id));

        // 2. Show
        $showResponse = $this->get(route('employees.show', $createdEmp->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Budi Santoso TPS');

        // 3. Edit
        $editResponse = $this->get(route('employees.edit', $createdEmp->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Data Pegawai: Budi Santoso TPS');

        // 4. Update
        $updateResponse = $this->put(route('employees.update', $createdEmp->id), [
            'name' => 'Budi Santoso TPS Senior',
            'username' => $testNik,
            'birth_date' => '1992-03-10',
            'position' => 'Senior Press Kaizen Specialist',
            'plant_id' => $plant->id,
            'division_id' => $division->id,
            'department_id' => $department->id,
            'current_tps_level' => 'KP4',
            'status' => 'active',
        ]);
        $updateResponse->assertRedirect(route('employees.show', $createdEmp->id));

        // 5. Delete
        $deleteResponse = $this->delete(route('employees.destroy', $createdEmp->id));
        $deleteResponse->assertRedirect(route('employees.index'));
        $this->assertNull(Employee::find($createdEmp->id));
    }

    public function test_candidate_pipeline_index_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('candidates.index'));
        $response->assertStatus(200);
        $response->assertSee('Candidate Promotion Pipeline');
        $response->assertSee('Total Kandidat');
    }

    public function test_candidate_omdd_assessment_detail_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_OMDD);

        $candidate = WorkshopCandidate::first();
        $this->assertNotNull($candidate);

        $response = $this->get(route('candidates.show', $candidate->id));
        $response->assertStatus(200);

        $response->assertSee('Rubrik Penilaian OMDD');
        $response->assertSee('Problem Identification & Grasp of Situation');
        $response->assertSee('Root Cause Analysis (5-Why & Fishbone)');
        $response->assertSee('Countermeasure Execution & Yokoten');
        $response->assertSee('Presentation & TPS Coaching Skill');
        $response->assertSee('Keputusan Kelulusan');
        $response->assertSee('LULUS (PASS)');
        $response->assertSee('Simpan Penilaian OMDD');
    }

    public function test_candidate_assessment_submission_persists(): void
    {
        $this->loginAs(UserAccount::ROLE_OMDD);

        $candidate = WorkshopCandidate::first();
        $this->assertNotNull($candidate);

        $response = $this->post(route('candidates.assessment', $candidate->id), [
            'ws_1' => 5,
            'ws_2' => 4,
            'ws_3' => 5,
            'ws_4' => 4,
            'ws_5' => 4,
            'ws_6' => 4,
            'ws_7' => 5,
            'final_decision' => 'fail',
            'notes' => 'Catatan verifikasi asesor OMDD evaluasi kelulusan.',
        ]);

        $response->assertRedirect(route('candidates.show', $candidate->id));

        $this->assertDatabaseHas('workshop_evaluations', [
            'employee_id' => $candidate->employee_id,
            'final_decision' => 'fail',
        ]);
    }

    public function test_tps_tier_kp4_page_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('tps.kp4'));
        $response->assertStatus(200);
        $response->assertSee('TPS Key Person 4 (KP 4)');
    }

    public function test_tps_tier_kp3_page_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('tps.kp3'));
        $response->assertStatus(200);
        $response->assertSee('TPS Key Person 3 (KP 3)');
    }

    public function test_tps_tier_advance_page_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('tps.advance'));
        $response->assertStatus(200);
        $response->assertSee('TPS Advance (KP 1 - KP 2)');
    }

    public function test_activity_crud_routes_work(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('activity.create'));
        $response->assertStatus(200);
        $response->assertSee('Registrasi Aktivitas & Proyek Kaizen', false);
    }

    public function test_analytics_dashboard_loads(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $response = $this->get(route('analytics'));
        $response->assertStatus(200);
        $response->assertSee('TPS Intelligence & Cadence Analytics', false);
        $response->assertSee('Persebaran Level TPS Antar Pabrik');
    }

    public function test_analytics_members_endpoint_lists_key_persons_by_plant_and_tier(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $plant = Plant::whereHas('employees', fn ($query) => $query->where('current_tps_level', 'KP4'))->first();

        if ($plant === null) {
            $this->markTestSkipped('Seed data has no plant with a KP4 employee.');
        }

        $response = $this->getJson(route('analytics.members', ['plant_id' => $plant->id, 'tier' => 'kp4']));

        $response->assertStatus(200);
        $response->assertJsonPath('plant', $plant->name);
        $response->assertJsonPath('tier', 'kp4');

        $expected = Employee::where('plant_id', $plant->id)->where('current_tps_level', 'KP4')->count();
        $this->assertGreaterThan(0, $expected);
        $this->assertSame($expected, $response->json('total'));

        foreach ($response->json('members') as $member) {
            $this->assertSame('KP4', $member['level']);
            $this->assertArrayHasKey('name', $member);
            $this->assertArrayHasKey('nik', $member);
            $this->assertArrayHasKey('url', $member);
        }
    }

    public function test_analytics_members_endpoint_validates_parameters(): void
    {
        $this->loginAs(UserAccount::ROLE_ADMIN);

        $this->getJson(route('analytics.members', ['plant_id' => 999999, 'tier' => 'kp4']))
            ->assertStatus(422);

        $this->getJson(route('analytics.members', ['plant_id' => Plant::orderBy('id')->value('id'), 'tier' => 'nope']))
            ->assertStatus(422);
    }

    public function test_karyawan_cannot_access_analytics_members(): void
    {
        $this->loginAs(UserAccount::ROLE_KARYAWAN);

        $this->getJson(route('analytics.members', ['plant_id' => 1, 'tier' => 'kp4']))
            ->assertStatus(403);
    }

    public function test_karyawan_cannot_access_admin_only_routes(): void
    {
        $this->loginAs(UserAccount::ROLE_KARYAWAN);

        $this->get(route('employees.index'))->assertStatus(403);
        $this->get(route('employees.create'))->assertStatus(403);
        $this->get(route('analytics'))->assertStatus(403);
    }

    public function test_role_login_flow(): void
    {
        $response = $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }
}
