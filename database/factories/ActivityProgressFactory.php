<?php

namespace Database\Factories;

use App\Models\ActivityPlan;
use App\Models\ActivityProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityProgress>
 */
class ActivityProgressFactory extends Factory
{
    protected $model = ActivityProgress::class;

    public function definition(): array
    {
        $updates = [
            'Observasi genba & identifikasi waste motion',
            'Penyusunan Standard Work Combination Sheet (SWCS)',
            'Trial layout baru dan perbaikan jalur feeding material',
            'Evaluasi takt time vs cycle time setelah perbaikan',
            'Sosialisasi SOP baru kepada seluruh operator line',
        ];

        return [
            'activity_plan_id' => ActivityPlan::factory(),
            'status_update' => fake()->randomElement($updates),
            'description' => fake()->paragraph(),
            'image_path' => 'uploads/progress/' . fake()->uuid() . '.jpg',
        ];
    }
}

