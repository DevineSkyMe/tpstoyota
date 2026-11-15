<?php

namespace Database\Factories;

use App\Models\UserAccount;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    protected $model = Workshop::class;

    public function definition(): array
    {
        $year = (int) date('Y');
        $batch = fake()->numberBetween(1, 4);

        $locations = [
            'Toyota Learning Center (TLC) Karawang',
            'Sunter Training Center Room TPS-1',
            'TPS Dojo Karawang Plant 3',
            'Toyota Institute Indonesia Auditorium',
        ];

        $startDate = fake()->dateTimeBetween('-1 month', '+3 months');
        $endDate = (clone $startDate)->modify('+5 days');

        return [
            'year' => $year,
            'batch' => $batch,
            'title' => "Workshop TPS-G {$year} Batch {$batch}",
            'location' => fake()->randomElement($locations),
            'quota' => fake()->randomElement([20, 25, 30, 35]),
            'status' => fake()->randomElement(['draft', 'scheduled', 'ongoing', 'completed']),
            'created_by' => UserAccount::factory()->admin(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }
}

