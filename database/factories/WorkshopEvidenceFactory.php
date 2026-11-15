<?php

namespace Database\Factories;

use App\Models\WorkshopEvidence;
use App\Models\WorkshopParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkshopEvidence>
 */
class WorkshopEvidenceFactory extends Factory
{
    protected $model = WorkshopEvidence::class;

    public function definition(): array
    {
        return [
            'participant_id' => WorkshopParticipant::factory()->confirmed(),
            'week_no' => fake()->numberBetween(1, 4),
            'title' => 'Bukti Minggu Ke-' . fake()->numberBetween(1, 4),
            'note' => fake()->sentence(),
            'file_path' => null,
            'link_url' => fake()->url(),
            'status' => WorkshopEvidence::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkshopEvidence::STATUS_APPROVED,
            'reviewed_at' => now(),
        ]);
    }
}
