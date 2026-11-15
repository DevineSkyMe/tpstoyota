<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityFollowupSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('activity_followups')->truncate();

        DB::table('activity_followups')->insert([
                        [
                'id' => 1,
                'activity_plan_id' => 1,
                'admin_id' => 1,
                'followup_note' => 'Mohon lengkapi data before-after berupa foto cycle time',
                'status' => 'escalated',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
                        [
                'id' => 2,
                'activity_plan_id' => 2,
                'admin_id' => 1,
                'followup_note' => 'Dibutuhkan koordinasi lanjutan dengan maintenance department untuk instalasi sensor',
                'status' => 'in_review',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
                        [
                'id' => 3,
                'activity_plan_id' => 3,
                'admin_id' => 1,
                'followup_note' => 'Data kaizen sudah ditinjau, silakan lanjutkan ke fase evaluasi Yamazumi',
                'status' => 'pending',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
                        [
                'id' => 4,
                'activity_plan_id' => 4,
                'admin_id' => 1,
                'followup_note' => 'Data kaizen sudah ditinjau, silakan lanjutkan ke fase evaluasi Yamazumi',
                'status' => 'in_review',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
                        [
                'id' => 5,
                'activity_plan_id' => 5,
                'admin_id' => 1,
                'followup_note' => 'Mohon lengkapi data before-after berupa foto cycle time',
                'status' => 'pending',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
                        [
                'id' => 6,
                'activity_plan_id' => 6,
                'admin_id' => 1,
                'followup_note' => 'Pastikan safety briefing dilakukan sebelum implementasi modifikasi tooling',
                'status' => 'in_review',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
        ]);

    }
}