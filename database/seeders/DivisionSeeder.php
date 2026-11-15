<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('divisions')->truncate();

        DB::table('divisions')->insert([
                        [
                'id' => 1,
                'name' => 'Production Division',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 2,
                'name' => 'Quality Assurance & Control Division',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 3,
                'name' => 'Manufacturing Engineering Division',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 4,
                'name' => 'Human Resources & General Affairs Division',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 5,
                'name' => 'Supply Chain & Logistics Division',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 48,
                'name' => 'PWPD',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 49,
                'name' => 'APPD',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 50,
                'name' => 'OMDD',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 51,
                'name' => 'PAD',
                'created_at' => '2026-11-15 01:27:36',
                'updated_at' => '2026-11-15 01:27:36',
            ],
                        [
                'id' => 52,
                'name' => 'EPK3D',
                'created_at' => '2026-11-15 01:27:40',
                'updated_at' => '2026-11-15 01:27:40',
            ],
                        [
                'id' => 53,
                'name' => 'PBOD',
                'created_at' => '2026-11-15 01:27:40',
                'updated_at' => '2026-11-15 01:27:40',
            ],
                        [
                'id' => 54,
                'name' => 'EPSD',
                'created_at' => '2026-11-15 01:27:42',
                'updated_at' => '2026-11-15 01:27:42',
            ],
                        [
                'id' => 55,
                'name' => 'PCD',
                'created_at' => '2026-11-15 01:27:45',
                'updated_at' => '2026-11-15 01:27:45',
            ],
                        [
                'id' => 56,
                'name' => 'VPLD',
                'created_at' => '2026-11-15 01:27:48',
                'updated_at' => '2026-11-15 01:27:48',
            ],
                        [
                'id' => 57,
                'name' => 'PED',
                'created_at' => '2026-11-15 01:27:54',
                'updated_at' => '2026-11-15 01:27:54',
            ],
        ]);

    }
}