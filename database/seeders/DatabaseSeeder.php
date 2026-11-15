<?php

namespace Database\Seeders;

use App\Models\ActivityFollowup;
use App\Models\ActivityPlan;
use App\Models\ActivityProgress;
use App\Models\BatchStat;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Message;
use App\Models\Plant;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopEvaluation;
use App\Models\WorkshopHrdReview;
use App\Models\WorkshopParticipant;
use App\Models\WorkshopScoreReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Master Plants
        $plantNames = [
            'Plant 1 Sunter 1',
            'Plant 2 Karawang 1',
            'Plant 3 Karawang 2',
            'Plant 4 Sunter 2 (Casting)',
            'Plant 5 Karawang 3 (Engine)',
        ];
        $plants = collect($plantNames)->map(fn ($name) => Plant::firstOrCreate(['name' => $name]));

        // 2. Master Divisions
        $divisionNames = [
            'Production Division',
            'Quality Assurance & Control Division',
            'Manufacturing Engineering Division',
            'Human Resources & General Affairs Division',
            'Supply Chain & Logistics Division',
        ];
        $divisions = collect($divisionNames)->map(fn ($name) => Division::firstOrCreate(['name' => $name]));

        // 3. Master Departments
        $deptNames = [
            'Press Department',
            'Welding Department',
            'Paint Department',
            'Assembly Department',
            'Quality Inspection Department',
            'Kaizen & TPS Promotion Department',
            'HRD & Training Center',
            'OMDD (Operations Management Development Division)',
        ];
        $departments = collect($deptNames)->map(fn ($name) => Department::firstOrCreate(['name' => $name]));

        // 4. Create Key Role User Accounts
        $admin = UserAccount::firstOrCreate(
            ['username' => 'admin_tps'],
            ['password' => Hash::make('password'), 'role' => 'admin']
        );

        $tlc = UserAccount::firstOrCreate(
            ['username' => 'tlc_reviewer'],
            ['password' => Hash::make('password'), 'role' => 'tlc']
        );

        $hrd = UserAccount::firstOrCreate(
            ['username' => 'hrd_reviewer'],
            ['password' => Hash::make('password'), 'role' => 'hrd']
        );

        $omdd = UserAccount::firstOrCreate(
            ['username' => 'omdd_evaluator'],
            ['password' => Hash::make('password'), 'role' => 'omdd']
        );

        $director = UserAccount::firstOrCreate(
            ['username' => 'director_tps'],
            ['password' => Hash::make('password'), 'role' => 'director']
        );

        // 5. Seed Employees with User Accounts
        $employees = collect();
        for ($i = 1; $i <= 12; $i++) {
            $employee = Employee::factory()->create([
                'plant_id' => $plants->random()->id,
                'division_id' => $divisions->random()->id,
                'department_id' => $departments->random()->id,
            ]);

            UserAccount::factory()->create([
                'username' => 'user_' . $employee->username,
                'password' => Hash::make('password'),
                'role' => 'participant',
                'employee_id' => $employee->id,
            ]);

            $employees->push($employee);
        }

        // 6. Seed Activity Plans, Progress Updates, and Followups
        foreach ($employees->take(6) as $emp) {
            $plan = ActivityPlan::factory()->create([
                'employee_id' => $emp->id,
            ]);

            ActivityProgress::factory()->count(2)->create([
                'activity_plan_id' => $plan->id,
            ]);

            ActivityFollowup::factory()->create([
                'activity_plan_id' => $plan->id,
                'admin_id' => $admin->id,
            ]);
        }

        // 7. Seed Workshops (Batch 1 completed, Batch 2 scheduled)
        $workshop1 = Workshop::factory()->create([
            'year' => (int) date('Y'),
            'batch' => 1,
            'title' => 'Workshop TPS-G ' . date('Y') . ' Batch 1',
            'status' => 'completed',
            'created_by' => $admin->id,
            'start_date' => now()->subMonths(2)->format('Y-m-d'),
            'end_date' => now()->subMonths(2)->addDays(5)->format('Y-m-d'),
        ]);

        $workshop2 = Workshop::factory()->create([
            'year' => (int) date('Y'),
            'batch' => 2,
            'title' => 'Workshop TPS-G ' . date('Y') . ' Batch 2',
            'status' => 'scheduled',
            'created_by' => $admin->id,
            'start_date' => now()->addMonth()->format('Y-m-d'),
            'end_date' => now()->addMonth()->addDays(5)->format('Y-m-d'),
        ]);

        // 8. Seed Candidates, HRD Reviews, Participants, Evaluations for Workshop 1
        foreach ($employees->take(8) as $index => $emp) {
            // Stage 1: TLC Candidate
            $candidate = WorkshopCandidate::factory()->create([
                'employee_id' => $emp->id,
                'recommended_by' => $tlc->id,
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => now()->subMonths(3),
            ]);

            // Stage 2: HRD Review
            WorkshopHrdReview::factory()->create([
                'candidate_id' => $candidate->id,
                'hrd_id' => $hrd->id,
                'hrd_status' => 'recommended',
                'reviewed_at' => now()->subMonths(2)->subWeeks(2),
            ]);

            // Stage 3: Admin Confirmation as Workshop Participant
            $participant = WorkshopParticipant::factory()->create([
                'workshop_id' => $workshop1->id,
                'employee_id' => $emp->id,
                'candidate_id' => $candidate->id,
                'confirmed_by' => $admin->id,
                'confirmation_status' => 'confirmed',
            ]);

            // Stage 4: OMDD Evaluation
            WorkshopEvaluation::factory()->create([
                'employee_id' => $emp->id,
                'workshop_id' => $workshop1->id,
                'participant_id' => $participant->id,
                'evaluated_by_id' => $omdd->id,
                'evaluated_by' => 'OMDD Lead Evaluator',
                'final_decision' => $index % 4 === 3 ? 'FAIL' : 'PASS',
                'evaluated_at' => now()->subMonths(2)->addDays(6),
            ]);
        }

        // 9. Stage 5: Workshop Score Report (Admin)
        $scoreReport = WorkshopScoreReport::factory()->create([
            'workshop_id' => $workshop1->id,
            'generated_by' => $admin->id,
            'pdf_path' => 'reports/score_reports/tpsg_batch_1_' . date('Y') . '_results.pdf',
            'sent_to_hrd_at' => now()->subMonths(2)->addDays(7),
            'sent_to_director_at' => now()->subMonths(2)->addDays(8),
            'status' => 'completed',
        ]);

        // 10. Chat Message Distribution
        Message::factory()->create([
            'sender_id' => $admin->id,
            'receiver_id' => $director->id,
            'related_type' => 'workshop_score_report',
            'related_id' => $scoreReport->id,
            'message' => 'Yth. Direktur TPS, berikut kami sampaikan Laporan Nilai Evaluasi Peserta Workshop TPS-G Batch 1 ' . date('Y') . ' untuk menjadi periksa.',
            'attachment_path' => $scoreReport->pdf_path,
            'is_read' => true,
            'read_at' => now()->subMonths(2)->addDays(8),
        ]);

        Message::factory()->create([
            'sender_id' => $admin->id,
            'receiver_id' => $hrd->id,
            'related_type' => 'workshop_score_report',
            'related_id' => $scoreReport->id,
            'message' => 'Laporan Hasil Workshop TPS-G Batch 1 telah selesai. Mohon dapat diproses untuk penerbitan SK kelulusan level KP3 & KP4.',
            'attachment_path' => $scoreReport->pdf_path,
            'is_read' => true,
            'read_at' => now()->subMonths(2)->addDays(7),
        ]);

        // 11. Batch Statistics
        BatchStat::factory()->create([
            'batch_name' => 'Batch 1 ' . date('Y'),
            'participant_count' => 8,
            'kp3_count' => 5,
            'kp4_count' => 1,
            'kp3_percent' => '62.5%',
            'kp4_percent' => '12.5%',
        ]);
    }
}
