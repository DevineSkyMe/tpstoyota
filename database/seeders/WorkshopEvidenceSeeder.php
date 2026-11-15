<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopEvidenceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshop_evidences')->truncate();

        DB::table('workshop_evidences')->insert([
                        [
                'id' => 14,
                'participant_id' => 9,
                'week_no' => 1,
                'title' => 'dsadasd',
                'note' => 'asdasdasd',
                'file_path' => 'uploads/workshop-evidence/ws2-p9-w1-1794657984.png',
                'link_url' => null,
                'status' => 'approved',
                'reviewed_by' => 1,
                'reviewed_at' => '2026-11-14 12:07:26',
                'review_note' => null,
                'submitted_at' => '2026-11-14 12:06:24',
                'created_at' => '2026-11-14 12:06:24',
                'updated_at' => '2026-11-14 12:07:26',
            ],
        ]);

    }
}