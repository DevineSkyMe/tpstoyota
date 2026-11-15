<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopCandidateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshop_candidates')->truncate();

        DB::table('workshop_candidates')->insert([
                        [
                'id' => 1,
                'employee_id' => 1,
                'recommended_by' => 2,
                'target_tps_level' => 'KP4',
                'tlc_reason' => 'Direkomendasikan untuk naik ke level TPS selanjutnya karena kepemimpinan dalam QCC.',
                'tlc_status' => 'approved',
                'tlc_reviewed_at' => '2026-06-20 09:34:17',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 17:27:18',
            ],
                        [
                'id' => 2,
                'employee_id' => 2,
                'recommended_by' => 2,
                'target_tps_level' => 'KP4',
                'tlc_reason' => 'Telah menyelesaikan proyek standard work improvement dengan reduksi cycle time 15%.',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 3,
                'employee_id' => 3,
                'recommended_by' => 2,
                'target_tps_level' => 'KP3',
                'tlc_reason' => 'Menunjukkan kemampuan problem solving A3 yang sangat baik di area stamping.',
                'tlc_status' => 'approved',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-11-14 13:23:45',
            ],
                        [
                'id' => 4,
                'employee_id' => 4,
                'recommended_by' => 2,
                'target_tps_level' => 'KP3',
                'tlc_reason' => 'Telah menyelesaikan proyek standard work improvement dengan reduksi cycle time 15%.',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 5,
                'employee_id' => 5,
                'recommended_by' => 2,
                'target_tps_level' => 'KP4',
                'tlc_reason' => 'Menunjukkan kemampuan problem solving A3 yang sangat baik di area stamping.',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 6,
                'employee_id' => 6,
                'recommended_by' => 2,
                'target_tps_level' => 'KP4',
                'tlc_reason' => 'Telah menyelesaikan proyek standard work improvement dengan reduksi cycle time 15%.',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 7,
                'employee_id' => 7,
                'recommended_by' => 2,
                'target_tps_level' => 'KP3',
                'tlc_reason' => 'Direkomendasikan untuk naik ke level TPS selanjutnya karena kepemimpinan dalam QCC.',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 8,
                'employee_id' => 8,
                'recommended_by' => 2,
                'target_tps_level' => 'KP4',
                'tlc_reason' => 'Menunjukkan kemampuan problem solving A3 yang sangat baik di area stamping.',
                'tlc_status' => 'recommended',
                'tlc_reviewed_at' => '2026-06-20 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 9,
                'employee_id' => 13,
                'recommended_by' => 18,
                'target_tps_level' => 'KP4',
                'tlc_reason' => 'Exceptional 5-Why problem-solving and Yokoten readiness across Karawang plants',
                'tlc_status' => 'approved',
                'tlc_reviewed_at' => '2026-09-06 17:24:17',
                'created_at' => '2026-09-20 17:10:29',
                'updated_at' => '2026-11-14 12:33:34',
            ],
        ]);

    }
}