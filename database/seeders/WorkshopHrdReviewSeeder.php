<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopHrdReviewSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshop_hrd_reviews')->truncate();

        DB::table('workshop_hrd_reviews')->insert([
                        [
                'id' => 1,
                'candidate_id' => 1,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Direkomendasikan ikut workshop batch ini, telah lolos pra-kualifikasi TPS dasar.',
                'reviewed_at' => '2026-07-06 09:34:17',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 2,
                'candidate_id' => 2,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Penundaan direkomendasikan karena bentrok dengan jadwal audit mutu division.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 3,
                'candidate_id' => 3,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Direkomendasikan ikut workshop batch ini, telah lolos pra-kualifikasi TPS dasar.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 4,
                'candidate_id' => 4,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Penundaan direkomendasikan karena bentrok dengan jadwal audit mutu division.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 5,
                'candidate_id' => 5,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Penundaan direkomendasikan karena bentrok dengan jadwal audit mutu division.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 6,
                'candidate_id' => 6,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Direkomendasikan ikut workshop batch ini, telah lolos pra-kualifikasi TPS dasar.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 7,
                'candidate_id' => 7,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Direkomendasikan ikut workshop batch ini, telah lolos pra-kualifikasi TPS dasar.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 8,
                'candidate_id' => 8,
                'hrd_id' => 3,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Lolos verifikasi data kepegawaian dan sertifikasi level TPS sebelumnya valid.',
                'reviewed_at' => '2026-07-06 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 9,
                'candidate_id' => 9,
                'hrd_id' => 18,
                'hrd_status' => 'recommended',
                'hrd_note' => 'Qualified for KP4 evaluation cadence. 4 Jishuken cycles completed.',
                'reviewed_at' => '2026-09-10 17:24:17',
                'created_at' => '2026-09-20 17:10:48',
                'updated_at' => '2026-09-20 17:24:17',
            ],
        ]);

    }
}