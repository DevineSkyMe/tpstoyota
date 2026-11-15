<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for clean re-seeding
        Schema::disableForeignKeyConstraints();

        $this->call(PlantSeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(UserAccountSeeder::class);
        $this->call(WorkshopSeeder::class);
        $this->call(BatchStatSeeder::class);
        $this->call(ActivityPlanSeeder::class);
        $this->call(ActivityProgressSeeder::class);
        $this->call(ActivityFollowupSeeder::class);
        $this->call(MessageSeeder::class);
        $this->call(WorkshopCandidateSeeder::class);
        $this->call(WorkshopHrdReviewSeeder::class);
        $this->call(WorkshopParticipantSeeder::class);
        $this->call(WorkshopEvaluationSeeder::class);
        $this->call(WorkshopEvidenceSeeder::class);
        $this->call(WorkshopScoreReportSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}