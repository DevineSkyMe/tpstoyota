<?php

namespace Database\Factories;

use App\Models\BatchStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BatchStat>
 */
class BatchStatFactory extends Factory
{
    protected $model = BatchStat::class;

    public function definition(): array
    {
        $participants = fake()->numberBetween(25, 40);
        $kp3 = fake()->numberBetween(15, (int) ($participants * 0.7));
        $kp4 = fake()->numberBetween(5, $participants - $kp3);

        $kp3Pct = number_format(($kp3 / $participants) * 100, 1) . '%';
        $kp4Pct = number_format(($kp4 / $participants) * 100, 1) . '%';

        return [
            'batch_name' => 'Batch ' . fake()->unique()->numberBetween(1, 99) . ' ' . date('Y'),
            'participant_count' => $participants,
            'kp3_count' => $kp3,
            'kp4_count' => $kp4,
            'kp3_percent' => $kp3Pct,
            'kp4_percent' => $kp4Pct,
        ];
    }
}

