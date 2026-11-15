<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plants')->truncate();

        DB::table('plants')->insert([
                        [
                'id' => 1,
                'name' => 'Plant 1 Sunter 1',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 2,
                'name' => 'Plant 2 Karawang 1',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 3,
                'name' => 'Plant 3 Karawang 2',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 4,
                'name' => 'Plant 4 Sunter 2 (Casting)',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 5,
                'name' => 'Plant 5 Karawang 3 (Engine)',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 36,
                'name' => 'KARAWANG 1',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 37,
                'name' => 'KARAWANG 2',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 38,
                'name' => 'HEAD OFFICE',
                'created_at' => '2026-11-15 01:27:37',
                'updated_at' => '2026-11-15 01:27:37',
            ],
                        [
                'id' => 39,
                'name' => 'KARAWANG 3',
                'created_at' => '2026-11-15 01:27:40',
                'updated_at' => '2026-11-15 01:27:40',
            ],
                        [
                'id' => 40,
                'name' => 'SUNTER 1',
                'created_at' => '2026-11-15 01:27:40',
                'updated_at' => '2026-11-15 01:27:40',
            ],
                        [
                'id' => 41,
                'name' => 'SUNTER 2',
                'created_at' => '2026-11-15 01:27:42',
                'updated_at' => '2026-11-15 01:27:42',
            ],
        ]);

    }
}