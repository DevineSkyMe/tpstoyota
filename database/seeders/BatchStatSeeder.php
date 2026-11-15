<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BatchStatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('batch_stats')->truncate();

        DB::table('batch_stats')->insert([
                        [
                'id' => 1,
                'batch_name' => 'Batch 1 2026',
                'participant_count' => 8,
                'kp3_count' => 5,
                'kp4_count' => 1,
                'kp3_percent' => '62.5%',
                'kp4_percent' => '12.5%',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
        ]);

    }
}