<?php

namespace Database\Factories;

use App\Models\ActivityPlan;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityPlan>
 */
class ActivityPlanFactory extends Factory
{
    protected $model = ActivityPlan::class;

    public function definition(): array
    {
        $categories = ['Workshop', 'Kaizen', 'Genba Kaizen', 'QCC Project', 'Standardization'];
        $statuses = ['not_started', 'in_progress', 'delay', 'completed'];
        $status = fake()->randomElement($statuses);

        $progressPct = match ($status) {
            'not_started' => 0,
            'completed' => 100,
            default => fake()->numberBetween(10, 90),
        };

        $plannedStart = fake()->dateTimeBetween('-2 months', '+1 month');
        $plannedEnd = (clone $plannedStart)->modify('+14 days');

        $actualStart = $status !== 'not_started' ? (clone $plannedStart)->modify('+1 day')->format('Y-m-d') : null;
        $actualEnd = $status === 'completed' ? (clone $plannedEnd)->modify('+2 days')->format('Y-m-d') : null;

        return [
            'title' => 'Aktivitas TPS: ' . fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement($categories),
            'planned_start' => $plannedStart->format('Y-m-d'),
            'planned_end' => $plannedEnd->format('Y-m-d'),
            'actual_start' => $actualStart,
            'actual_end' => $actualEnd,
            'status' => $status,
            'progress_pct' => $progressPct,
            'pic' => fake()->name(),
            'notes' => fake()->sentence(),
            'batch' => 'Batch ' . fake()->numberBetween(1, 4) . ' ' . date('Y'),
            'image_path' => 'uploads/evidence/' . fake()->uuid() . '.jpg',
            'employee_id' => Employee::factory(),
        ];
    }
}

