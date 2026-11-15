<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopParticipantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshop_participants')->truncate();

        DB::table('workshop_participants')->insert([
                        [
                'id' => 1,
                'workshop_id' => 1,
                'employee_id' => 1,
                'candidate_id' => 1,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 2,
                'workshop_id' => 1,
                'employee_id' => 2,
                'candidate_id' => 2,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 3,
                'workshop_id' => 1,
                'employee_id' => 3,
                'candidate_id' => 3,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 5,
                'workshop_id' => 1,
                'employee_id' => 5,
                'candidate_id' => 5,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 6,
                'workshop_id' => 1,
                'employee_id' => 6,
                'candidate_id' => 6,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 7,
                'workshop_id' => 1,
                'employee_id' => 7,
                'candidate_id' => 7,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 8,
                'workshop_id' => 1,
                'employee_id' => 8,
                'candidate_id' => 8,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 9,
                'workshop_id' => 2,
                'employee_id' => 13,
                'candidate_id' => 9,
                'confirmed_by' => 18,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-09-20 17:11:12',
                'updated_at' => '2026-09-20 17:11:12',
            ],
                        [
                'id' => 30,
                'workshop_id' => 2,
                'employee_id' => 11,
                'candidate_id' => null,
                'confirmed_by' => 1,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-11-14 12:31:38',
                'updated_at' => '2026-11-14 12:31:38',
            ],
                        [
                'id' => 31,
                'workshop_id' => 2,
                'employee_id' => 3,
                'candidate_id' => 3,
                'confirmed_by' => 4,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-11-14 13:23:45',
                'updated_at' => '2026-11-14 13:23:45',
            ],
                        [
                'id' => 32,
                'workshop_id' => 2,
                'employee_id' => 5,
                'candidate_id' => 5,
                'confirmed_by' => 4,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-11-14 13:25:03',
                'updated_at' => '2026-11-14 13:25:03',
            ],
                        [
                'id' => 33,
                'workshop_id' => 2,
                'employee_id' => 2,
                'candidate_id' => 2,
                'confirmed_by' => 4,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-11-14 13:25:25',
                'updated_at' => '2026-11-14 13:25:25',
            ],
                        [
                'id' => 34,
                'workshop_id' => 2,
                'employee_id' => 1,
                'candidate_id' => 1,
                'confirmed_by' => 4,
                'confirmation_status' => 'confirmed',
                'created_at' => '2026-11-15 01:21:48',
                'updated_at' => '2026-11-15 01:21:48',
            ],
        ]);

    }
}