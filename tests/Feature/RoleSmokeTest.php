<?php

namespace Tests\Feature;

use App\Models\ActivityPlan;
use App\Models\Employee;
use App\Models\Message;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopParticipant;
use Tests\TestCase;

class RoleSmokeTest extends TestCase
{
    private function as(string $role): void
    {
        $user = UserAccount::where('role', $role)->orderBy('id')->firstOrFail();
        $this->actingAs($user);
    }

    private function check(string $label, string $url): void
    {
        $res = $this->get($url);
        $this->assertTrue(
            $res->getStatusCode() < 500,
            "[$label] $url returned {$res->getStatusCode()}"
        );
    }

    private function forbidden(string $label, string $url): void
    {
        $res = $this->get($url);
        $this->assertSame(
            403,
            $res->getStatusCode(),
            "[$label] $url expected 403, got {$res->getStatusCode()}"
        );
    }

    private function common(): array
    {
        $employee = Employee::orderBy('id')->first();
        $activity = ActivityPlan::orderBy('id')->first();
        $workshop = Workshop::orderBy('id')->first();
        $candidate = WorkshopCandidate::orderBy('id')->first();
        $message = Message::orderBy('id')->first();
        $participant = WorkshopParticipant::orderBy('id')->first();

        return [
            'employee' => $employee?->id,
            'activity' => $activity?->id,
            'workshop' => $workshop?->id,
            'candidate' => $candidate?->id,
            'message' => $message?->id,
            'participant' => $participant?->id,
        ];
    }

    public function test_karyawan_pages_render(): void
    {
        $this->as(UserAccount::ROLE_KARYAWAN);
        $c = $this->common();

        $karyawan = UserAccount::where('role', UserAccount::ROLE_KARYAWAN)->orderBy('id')->firstOrFail();
        $joined = WorkshopParticipant::where('employee_id', $karyawan->employee_id)->first();

        $this->check('karyawan', '/');
        $this->check('karyawan', '/activity');
        $this->check('karyawan', '/activity/create');
        if ($c['activity']) {
            $this->check('karyawan', "/activity/{$c['activity']}");
        }
        $this->check('karyawan', '/workshops');
        if ($joined) {
            $this->check('karyawan', "/workshops/{$joined->workshop_id}");
        }
        $this->check('karyawan', '/profile');
        $this->check('karyawan', '/messages');
        $this->check('karyawan', '/search?q=kaizen');
    }

    public function test_admin_pages_render(): void
    {
        $this->as(UserAccount::ROLE_ADMIN);
        $c = $this->common();

        foreach (['/', '/activity', '/activity/create', '/workshops', '/workshops/create', '/employees', '/employees/create', '/candidates', '/candidates/create', '/evaluations', '/reports', '/reports/activity', '/messages', '/tps/kp4', '/tps/kp3', '/tps/advance', '/analytics', '/profile'] as $url) {
            $this->check('admin', $url);
        }
        if ($c['activity']) {
            $this->check('admin', "/activity/{$c['activity']}");
            $this->check('admin', "/activity/{$c['activity']}/edit");
        }
        if ($c['workshop']) {
            $this->check('admin', "/workshops/{$c['workshop']}");
            $this->check('admin', "/workshops/{$c['workshop']}/edit");
            $this->check('admin', "/reports/workshop/{$c['workshop']}");
        }
        if ($c['employee']) {
            $this->check('admin', "/employees/{$c['employee']}");
            $this->check('admin', "/employees/{$c['employee']}/edit");
        }
        if ($c['candidate']) {
            $this->check('admin', "/candidates/{$c['candidate']}");
        }
        if ($c['message']) {
            $this->check('admin', "/messages/{$c['message']}");
        }
    }

    public function test_hrd_pages_render(): void
    {
        $this->as(UserAccount::ROLE_HRD);
        $c = $this->common();

        // HRD workspace: dashboard, recommendations, reports, inbox, profile.
        foreach (['/', '/candidates', '/candidates/create', '/reports', '/reports/activity', '/messages', '/profile'] as $url) {
            $this->check('hrd', $url);
        }
        if ($c['candidate']) {
            $this->check('hrd', "/candidates/{$c['candidate']}");
        }
        if ($c['workshop']) {
            $this->check('hrd', "/reports/workshop/{$c['workshop']}");
        }

        // HRD may not browse activity, workshops, employees, TPS tiers or analytics.
        foreach (['/activity', '/activity/create', '/workshops', '/employees', '/employees/create', '/tps/kp4', '/tps/kp3', '/tps/advance', '/analytics', '/evaluations'] as $url) {
            $this->forbidden('hrd', $url);
        }
        if ($c['activity']) {
            $this->forbidden('hrd', "/activity/{$c['activity']}");
        }
        if ($c['workshop']) {
            $this->forbidden('hrd', "/workshops/{$c['workshop']}");
        }
        if ($c['employee']) {
            $this->forbidden('hrd', "/employees/{$c['employee']}");
        }
    }

    public function test_omdd_pages_render(): void
    {
        $this->as(UserAccount::ROLE_OMDD);
        $c = $this->common();

        foreach (['/', '/activity', '/workshops', '/employees', '/candidates', '/evaluations', '/messages', '/tps/kp4', '/tps/kp3', '/tps/advance', '/analytics', '/profile'] as $url) {
            $this->check('omdd', $url);
        }
        if ($c['candidate']) {
            $this->check('omdd', "/candidates/{$c['candidate']}");
        }
        if ($c['participant']) {
            $this->check('omdd', "/evaluations/{$c['participant']}");
        }
    }

    public function test_manager_pages_render(): void
    {
        $this->as(UserAccount::ROLE_MANAGER);
        $c = $this->common();

        foreach (['/', '/activity', '/workshops', '/employees', '/candidates', '/reports', '/reports/activity', '/messages', '/tps/kp4', '/tps/kp3', '/tps/advance', '/analytics', '/profile'] as $url) {
            $this->check('manager', $url);
        }
        if ($c['employee']) {
            $this->check('manager', "/employees/{$c['employee']}");
        }
    }
}
