<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workshops')->truncate();

        DB::table('workshops')->insert([
                        [
                'id' => 1,
                'year' => 2026,
                'batch' => 1,
                'title' => 'Workshop TPS-G 2026 Batch 1',
                'location' => 'Toyota Learning Center (TLC) Karawang',
                'quota' => 25,
                'status' => 'completed',
                'created_by' => 1,
                'start_date' => '2026-07-20',
                'end_date' => '2026-07-25',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
                        [
                'id' => 2,
                'year' => 2026,
                'batch' => 2,
                'title' => 'Workshop TPS-G 2026 Batch 2',
                'location' => 'Sunter Training Center Room TPS-1',
                'quota' => 30,
                'status' => 'scheduled',
                'created_by' => 1,
                'start_date' => '2026-11-28',
                'end_date' => '2026-12-03',
                'created_at' => '2026-09-20 09:34:17',
                'updated_at' => '2026-09-20 09:34:17',
            ],
        ]);

    }
}