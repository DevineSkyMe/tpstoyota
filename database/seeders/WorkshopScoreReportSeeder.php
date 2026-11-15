<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopScoreReportSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshop_score_reports')->truncate();

        DB::table('workshop_score_reports')->insert([
                        [
                'id' => 1,
                'workshop_id' => 1,
                'generated_by' => 1,
                'pdf_path' => 'http://127.0.0.1:8000/reports/workshop/1',
                'generated_at' => '2026-11-15 02:35:49',
                'sent_to_hrd_at' => '2026-11-15 02:16:30',
                'sent_to_director_at' => '2026-11-15 02:16:30',
                'status' => 'completed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-11-15 02:36:01',
            ],
                        [
                'id' => 2,
                'workshop_id' => 2,
                'generated_by' => 1,
                'pdf_path' => 'http://127.0.0.1:8000/reports/workshop/2',
                'generated_at' => '2026-11-14 12:31:47',
                'sent_to_hrd_at' => '2026-11-14 12:31:44',
                'sent_to_director_at' => '2026-11-14 12:31:44',
                'status' => 'generated',
                'created_at' => '2026-11-14 02:11:24',
                'updated_at' => '2026-11-14 12:31:47',
            ],
        ]);

    }
}