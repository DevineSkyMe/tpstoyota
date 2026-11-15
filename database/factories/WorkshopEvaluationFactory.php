<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopEvaluation;
use App\Models\WorkshopParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkshopEvaluation>
 */
class WorkshopEvaluationFactory extends Factory
{
    protected $model = WorkshopEvaluation::class;

    public function definition(): array
    {
        $scores = [];
        for ($i = 1; $i <= 7; $i++) {
            $scores["ws_{$i}"] = fake()->numberBetween(65, 98);
        }

        $avg = array_sum($scores) / 7;
        $decision = $avg >= 75 ? 'PASS' : 'FAIL';

        $notes = [
            'Pemahaman TPS mindset & penerapan genba kaizen sangat solid.',
            'Kemampuan kalkulasi takt time dan penyusunan standardized work sangat baik.',
            'Perlu penguatan dalam identifikasi 7 muda di area handling & logistik.',
            'Lulus dengan predikat memuaskan, presentasi A3 report sangat komprehensif.',
        ];

        return array_merge($scores, [
            'employee_id' => Employee::factory(),
            'workshop_id' => Workshop::factory(),
            'participant_id' => WorkshopParticipant::factory(),
            'final_decision' => $decision,
            'notes' => fake()->randomElement($notes),
            'evaluated_by' => 'OMDD Master Trainer ' . fake()->firstName(),
            'evaluated_by_id' => UserAccount::factory()->omdd(),
            'evaluated_at' => now(),
        ]);
    }

    public function pass(): static
    {
        return $this->state(fn (array $attributes) => [
            'ws_1' => fake()->numberBetween(80, 95),
            'ws_2' => fake()->numberBetween(80, 95),
            'ws_3' => fake()->numberBetween(80, 95),
            'ws_4' => fake()->numberBetween(80, 95),
            'ws_5' => fake()->numberBetween(80, 95),
            'ws_6' => fake()->numberBetween(80, 95),
            'ws_7' => fake()->numberBetween(80, 95),
            'final_decision' => 'PASS',
        ]);
    }

    public function fail(): static
    {
        return $this->state(fn (array $attributes) => [
            'ws_1' => fake()->numberBetween(50, 70),
            'ws_2' => fake()->numberBetween(50, 70),
            'ws_3' => fake()->numberBetween(50, 70),
            'ws_4' => fake()->numberBetween(50, 70),
            'ws_5' => fake()->numberBetween(50, 70),
            'ws_6' => fake()->numberBetween(50, 70),
            'ws_7' => fake()->numberBetween(50, 70),
            'final_decision' => 'FAIL',
        ]);
    }
}

