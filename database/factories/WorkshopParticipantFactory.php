<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkshopParticipant>
 */
class WorkshopParticipantFactory extends Factory
{
    protected $model = WorkshopParticipant::class;

    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'employee_id' => Employee::factory(),
            'candidate_id' => WorkshopCandidate::factory(),
            'confirmed_by' => UserAccount::factory()->admin(),
            'confirmation_status' => fake()->randomElement(['confirmed', 'waitlist', 'cancelled']),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmation_status' => 'confirmed',
        ]);
    }
}

