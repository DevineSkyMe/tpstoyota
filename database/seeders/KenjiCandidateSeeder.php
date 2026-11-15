<?php

namespace Database\Seeders;

use App\Models\ActivityPlan;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Plant;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopEvaluation;
use App\Models\WorkshopHrdReview;
use App\Models\WorkshopParticipant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KenjiCandidateSeeder extends Seeder
{
    public function run(): void
    {
        $plant = Plant::firstOrCreate(['name' => 'Plant 2 Karawang 1']);
        $division = Division::firstOrCreate(['name' => 'Production Division']);
        $department = Department::firstOrCreate(['name' => 'Welding Department']);

        // Assessor: Hiroshi Sato
        $assessor = UserAccount::firstOrCreate(
            ['username' => 'hiroshi_sato'],
            [
                'password' => Hash::make('password'),
                'role' => 'omdd',
            ]
        );

        // Kenji Takahashi
        $employee = Employee::updateOrCreate(
            ['username' => '51204'],
            [
                'name' => 'Kenji Takahashi',
                'birth_date' => '1989-05-14',
                'position' => 'Body Shop & Robot Welding Specialist',
                'current_tps_level' => 'TPS KP 3',
                'previous_tps_level' => 'TPS Advance',
                'tahun_lulus_terakhir' => 2023,
                'tahun_lulus_saat_ini' => 2026,
                'last_activity_theme' => 'Robot Welding Station 4 AI Vision Quality Inspection',
                'last_activity_type' => 'Jishuken & Genba Kaizen',
                'batch' => 2,
                'registration_year' => 2024,
                'status' => 'active',
                'plant_id' => $plant->id,
                'division_id' => $division->id,
                'department_id' => $department->id,
            ]
        );

        // Account for Kenji
        UserAccount::firstOrCreate(
            ['username' => 'kenji_takahashi'],
            [
                'password' => Hash::make('password'),
                'role' => 'participant',
                'employee_id' => $employee->id,
            ]
        );

        // Activity plans for Kenji
        ActivityPlan::firstOrCreate(
            ['employee_id' => $employee->id, 'title' => 'Vision AI Inspection at Robot Welding Station 4'],
            [
                'category' => 'Kaizen Jishuken',
                'description' => 'Reduce false weld rejects from 3.8% to <0.1% using high-speed camera sensors',
                'planned_start' => now()->subMonths(3)->format('Y-m-d'),
                'planned_end' => now()->addMonth()->format('Y-m-d'),
                'status' => 'in_progress',
                'progress_pct' => 86,
                'pic' => 'Kenji Takahashi',
                'notes' => 'A3 report verified with Team Leader and certified for yokoten review.',
                'batch' => 'Batch 2',
            ]
        );

        // Workshop Batch
        $workshop = Workshop::firstOrCreate(
            ['year' => 2026, 'batch' => 2],
            [
                'title' => 'Workshop TPS-G 2026 Batch 2',
                'status' => 'in_progress',
                'created_by' => $assessor->id,
                'start_date' => now()->subDays(5)->format('Y-m-d'),
                'end_date' => now()->addDays(20)->format('Y-m-d'),
            ]
        );

        // Candidate
        $candidate = WorkshopCandidate::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'recommended_by' => $assessor->id,
                'target_tps_level' => 'KP 4',
                'tlc_reason' => 'Exceptional 5-Why problem-solving and Yokoten readiness across Karawang plants',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => now()->subWeeks(2),
            ]
        );

        // HRD Review
        WorkshopHrdReview::updateOrCreate(
            ['candidate_id' => $candidate->id],
            [
                'hrd_id' => $assessor->id,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Qualified for KP4 evaluation cadence. 4 Jishuken cycles completed.',
                'reviewed_at' => now()->subDays(10),
            ]
        );

        // Participant
        $participant = WorkshopParticipant::firstOrCreate(
            ['workshop_id' => $workshop->id, 'employee_id' => $employee->id],
            [
                'candidate_id' => $candidate->id,
                'confirmed_by' => $assessor->id,
                'confirmation_status' => 'confirmed',
            ]
        );

        // Evaluation
        WorkshopEvaluation::updateOrCreate(
            ['employee_id' => $employee->id, 'workshop_id' => $workshop->id],
            [
                'participant_id' => $participant->id,
                'ws_1' => 5, // Problem Identification: 4.8 / 5.0
                'ws_2' => 4, // RCA 5-Why: 4.2 / 5.0
                'ws_3' => 5, // Yokoten: 4.6 / 5.0
                'ws_4' => 4, // Coaching & A3: 4.0 / 5.0
                'ws_5' => 5,
                'ws_6' => 4,
                'ws_7' => 5,
                'final_decision' => 'pass',
                'notes' => 'Kandidat Kenji Takahashi menunjukkan kapabilitas luar biasa dalam memimpin proyek Implementasi Vision AI Inspection di Robot Welding Station 4. Analisis 5-Why dijalankan sangat mendalam, berhasil mengeliminasi akar masalah false reject dari 3.8% menjadi 0.05%. Pendekatan standardisasi A3 telah direview bersama team operator dan disetujui untuk replikasi Yokoten ke Plant Karawang 2.',
                'evaluated_by' => 'Hiroshi Sato (OMDD Division)',
                'evaluated_by_id' => $assessor->id,
                'evaluated_at' => now()->subMinutes(14),
            ]
        );
    }
}
