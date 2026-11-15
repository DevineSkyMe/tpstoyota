<?php

namespace Tests\Feature;

use App\Models\ActivityPlan;
use App\Models\ActivityProgress;
use App\Models\Employee;
use App\Models\Message;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopEvidence;
use App\Models\WorkshopParticipant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * End-to-end write flows for each role. Wrapped in a transaction so the
 * shared development database is left untouched.
 */
class RoleWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function as(string $role): UserAccount
    {
        $user = UserAccount::where('role', $role)->orderBy('id')->firstOrFail();
        $this->actingAs($user);

        return $user;
    }

    public function test_karyawan_can_create_an_activity(): void
    {
        $user = $this->as(UserAccount::ROLE_KARYAWAN);

        $response = $this->post(route('activity.store'), [
            'title' => 'Kaizen Uji Otomatis',
            'description' => 'Pengujian alur pembuatan activity oleh karyawan.',
            'category' => 'Quality',
            'planned_start' => now()->toDateString(),
            'planned_end' => now()->addDays(7)->toDateString(),
            'status' => 'in_progress',
            'progress_pct' => 10,
            'notes' => 'Dibuat oleh test.',
        ]);

        $response->assertSessionHasNoErrors();
        $activity = ActivityPlan::where('title', 'Kaizen Uji Otomatis')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($user->employee_id, $activity->employee_id);
        $response->assertRedirect(route('activity.show', $activity->id));
    }

    public function test_karyawan_only_sees_joined_workshops(): void
    {
        $user = $this->as(UserAccount::ROLE_KARYAWAN);

        $joined = WorkshopParticipant::where('employee_id', $user->employee_id)
            ->where('confirmation_status', 'confirmed')
            ->first();
        $this->assertNotNull($joined, 'Akun karyawan demo harus menjadi peserta minimal satu workshop.');

        // A workshop this employee has NOT joined.
        $admin = UserAccount::where('role', UserAccount::ROLE_ADMIN)->orderBy('id')->firstOrFail();
        $other = Workshop::create([
            'year' => 2099,
            'batch' => 99,
            'title' => 'Workshop Rahasia Batch Lain',
            'status' => 'scheduled',
            'start_date' => now()->addMonth()->toDateString(),
            'end_date' => now()->addMonth()->addDays(5)->toDateString(),
            'created_by' => $admin->id,
        ]);

        // Can open a workshop they joined.
        $this->get(route('workshops.show', $joined->workshop_id))->assertStatus(200);

        // Cannot open a workshop they did not join.
        $this->get(route('workshops.show', $other->id))->assertStatus(403);

        // The catalogue lists only joined workshops.
        $response = $this->get(route('workshops.index'));
        $response->assertStatus(200);
        $response->assertSee($joined->workshop->title);
        $response->assertDontSee($other->title);
    }

    public function test_karyawan_can_submit_weekly_evidence(): void
    {
        $user = $this->as(UserAccount::ROLE_KARYAWAN);

        $participant = WorkshopParticipant::where('employee_id', $user->employee_id)
            ->where('confirmation_status', 'confirmed')
            ->first();
        $this->assertNotNull($participant);

        WorkshopEvidence::where('participant_id', $participant->id)->where('week_no', 1)->delete();

        $response = $this->post(route('workshops.evidence.store', $participant->workshop_id), [
            'week_no' => 1,
            'title' => 'Bukti Minggu 1',
            'note' => 'Mengikuti sesi pembukaan workshop.',
            'link_url' => 'https://example.com/bukti-minggu-1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('workshop_evidences', [
            'participant_id' => $participant->id,
            'week_no' => 1,
            'status' => WorkshopEvidence::STATUS_SUBMITTED,
        ]);
    }

    public function test_karyawan_can_upload_an_evidence_file(): void
    {
        $user = $this->as(UserAccount::ROLE_KARYAWAN);

        $participant = WorkshopParticipant::where('employee_id', $user->employee_id)
            ->where('confirmation_status', 'confirmed')
            ->first();
        $this->assertNotNull($participant);

        WorkshopEvidence::where('participant_id', $participant->id)->where('week_no', 2)->delete();

        $response = $this->post(route('workshops.evidence.store', $participant->workshop_id), [
            'week_no' => 2,
            'title' => 'Bukti File Minggu 2',
            'evidence' => UploadedFile::fake()->create('bukti.pdf', 120, 'application/pdf'),
        ]);

        $response->assertSessionHasNoErrors();

        $evidence = WorkshopEvidence::where('participant_id', $participant->id)->where('week_no', 2)->first();
        $this->assertNotNull($evidence);
        $this->assertNotNull($evidence->file_path);
        $this->assertFileExists(public_path($evidence->file_path));

        // The DB row rolls back with the transaction; remove the physical file.
        @unlink(public_path($evidence->file_path));
    }

    public function test_omdd_can_review_weekly_evidence(): void
    {
        $this->as(UserAccount::ROLE_OMDD);

        $participant = WorkshopParticipant::where('confirmation_status', 'confirmed')->orderBy('id')->first();
        $this->assertNotNull($participant);

        $evidence = WorkshopEvidence::updateOrCreate(
            ['participant_id' => $participant->id, 'week_no' => 1],
            [
                'status' => WorkshopEvidence::STATUS_SUBMITTED,
                'note' => 'Bukti untuk pengujian review.',
                'submitted_at' => now(),
            ]
        );

        $response = $this->patch(route('workshops.evidence.review', [$participant->workshop_id, $evidence->id]), [
            'status' => 'approved',
            'review_note' => 'Bukti valid.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('workshop_evidences', [
            'id' => $evidence->id,
            'status' => WorkshopEvidence::STATUS_APPROVED,
        ]);
    }

    public function test_admin_can_add_a_participant_to_a_workshop(): void
    {
        $this->as(UserAccount::ROLE_ADMIN);

        $workshop = Workshop::orderBy('id')->first();
        $this->assertNotNull($workshop);

        $existing = $workshop->participants()->pluck('employee_id');
        $employee = Employee::whereNotIn('id', $existing)->orderBy('id')->first();
        $this->assertNotNull($employee, 'Butuh pegawai yang belum menjadi peserta.');

        $response = $this->post(route('workshops.participants.store', $workshop->id), [
            'employee_id' => $employee->id,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('workshop_participants', [
            'workshop_id' => $workshop->id,
            'employee_id' => $employee->id,
            'confirmation_status' => 'confirmed',
        ]);
    }

    public function test_admin_can_generate_and_send_a_workshop_report(): void
    {
        $this->as(UserAccount::ROLE_ADMIN);

        $workshop = Workshop::orderBy('id')->first();
        $this->assertNotNull($workshop);

        $this->post(route('reports.workshop.generate', $workshop->id))->assertRedirect();

        $before = Message::where('related_type', 'workshop_score_report')->count();

        $this->post(route('reports.workshop.send', $workshop->id), ['target' => 'both'])
            ->assertRedirect();

        $after = Message::where('related_type', 'workshop_score_report')->count();
        $this->assertGreaterThan($before, $after, 'Laporan harus terkirim sebagai pesan ke HRD/Manager.');
    }

    public function test_hrd_can_recommend_a_candidate(): void
    {
        $this->as(UserAccount::ROLE_HRD);

        $existing = WorkshopCandidate::pluck('employee_id');
        $employee = Employee::where('status', 'active')
            ->whereNotIn('id', $existing)
            ->get()
            ->first(fn (Employee $e) => $e->canPromote());

        $this->assertNotNull($employee, 'Butuh pegawai yang belum menjadi kandidat dan masih dapat naik level.');

        $response = $this->post(route('candidates.store'), [
            'employee_id' => $employee->id,
            'target_tps_level' => $employee->getNextTpsLevel(),
            'tlc_reason' => 'Rekomendasi otomatis untuk pengujian alur HRD.',
        ]);
        $response->assertRedirect(route('candidates.index'));

        $this->assertDatabaseHas('workshop_candidates', [
            'employee_id' => $employee->id,
            'tlc_status' => 'recommended',
        ]);
    }

    public function test_manager_can_view_reports(): void
    {
        $this->as(UserAccount::ROLE_MANAGER);

        $this->get(route('reports.index'))->assertStatus(200);
        $this->get(route('reports.activity'))->assertStatus(200);
    }

    public function test_hrd_can_view_reports_but_not_activity_or_workshops(): void
    {
        $this->as(UserAccount::ROLE_HRD);

        // Reports issued by Admin are viewable by HRD.
        $this->get(route('reports.index'))->assertStatus(200);
        $this->get(route('reports.activity'))->assertStatus(200);

        // HRD is locked out of activity, workshops, staff directories and analytics.
        $this->get(route('activity'))->assertStatus(403);
        $this->get(route('activity.create'))->assertStatus(403);
        $this->get(route('workshops.index'))->assertStatus(403);
        $this->get(route('employees.index'))->assertStatus(403);
        $this->get(route('tps.kp4'))->assertStatus(403);
        $this->get(route('analytics'))->assertStatus(403);
    }

    public function test_karyawan_can_submit_progress_on_own_activity(): void
    {
        $user = $this->as(UserAccount::ROLE_KARYAWAN);

        $activity = ActivityPlan::create([
            'title' => 'Kaizen Progress Otomatis',
            'category' => 'Quality',
            'planned_start' => now()->toDateString(),
            'planned_end' => now()->addDays(7)->toDateString(),
            'status' => 'in_progress',
            'progress_pct' => 20,
            'employee_id' => $user->employee_id,
        ]);

        $response = $this->post(route('activity.progress.store', $activity->id), [
            'status_update' => 'Observasi genba & identifikasi waste',
            'description' => 'Menemukan waste motion pada jalur feeding.',
            'progress_pct' => 40,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('activity_progress', [
            'activity_plan_id' => $activity->id,
            'submitted_by' => $user->id,
            'approval_status' => ActivityProgress::STATUS_PENDING,
            'progress_pct' => 40,
        ]);

        // Unapproved progress must not change the official activity figure.
        $this->assertEquals(20, $activity->fresh()->progress_pct);
    }

    public function test_karyawan_cannot_submit_progress_on_another_activity(): void
    {
        $user = $this->as(UserAccount::ROLE_KARYAWAN);

        $other = Employee::where('id', '!=', $user->employee_id)->orderBy('id')->firstOrFail();

        $activity = ActivityPlan::create([
            'title' => 'Kaizen Milik Orang Lain',
            'category' => 'Quality',
            'planned_start' => now()->toDateString(),
            'planned_end' => now()->addDays(7)->toDateString(),
            'status' => 'in_progress',
            'progress_pct' => 0,
            'employee_id' => $other->id,
        ]);

        $this->post(route('activity.progress.store', $activity->id), [
            'status_update' => 'Percobaan akses tidak sah',
        ])->assertStatus(403);
    }

    public function test_admin_can_acc_progress_with_feedback(): void
    {
        $admin = $this->as(UserAccount::ROLE_ADMIN);

        $owner = UserAccount::where('role', UserAccount::ROLE_KARYAWAN)->orderBy('id')->firstOrFail();

        $activity = ActivityPlan::create([
            'title' => 'Kaizen Untuk ACC',
            'category' => 'Quality',
            'planned_start' => now()->toDateString(),
            'planned_end' => now()->addDays(7)->toDateString(),
            'status' => 'in_progress',
            'progress_pct' => 10,
            'employee_id' => $owner->employee_id,
        ]);

        $progress = ActivityProgress::create([
            'activity_plan_id' => $activity->id,
            'submitted_by' => $owner->id,
            'status_update' => 'Trial layout baru',
            'progress_pct' => 65,
            'approval_status' => ActivityProgress::STATUS_PENDING,
        ]);

        $response = $this->patch(route('activity.progress.review', [$activity->id, $progress->id]), [
            'approval_status' => 'approved',
            'admin_note' => 'Bagus, lanjutkan ke fase evaluasi.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_progress', [
            'id' => $progress->id,
            'approval_status' => ActivityProgress::STATUS_APPROVED,
            'admin_note' => 'Bagus, lanjutkan ke fase evaluasi.',
            'reviewed_by' => $admin->id,
        ]);

        // Approved progress becomes the activity's official figure.
        $this->assertEquals(65, $activity->fresh()->progress_pct);
    }

    public function test_admin_can_add_a_monitoring_followup(): void
    {
        $admin = $this->as(UserAccount::ROLE_ADMIN);

        $activity = ActivityPlan::orderBy('id')->firstOrFail();

        $response = $this->post(route('activity.followup.store', $activity->id), [
            'followup_note' => 'Pantau keselamatan sebelum implementasi tooling.',
            'status' => 'in_review',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_followups', [
            'activity_plan_id' => $activity->id,
            'admin_id' => $admin->id,
            'followup_note' => 'Pantau keselamatan sebelum implementasi tooling.',
            'status' => 'in_review',
        ]);
    }
}
