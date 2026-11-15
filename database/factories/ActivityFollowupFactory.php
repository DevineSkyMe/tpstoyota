<?php

namespace Database\Factories;

use App\Models\ActivityFollowup;
use App\Models\ActivityPlan;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityFollowup>
 */
class ActivityFollowupFactory extends Factory
{
    protected $model = ActivityFollowup::class;

    public function definition(): array
    {
        $notes = [
            'Mohon lengkapi data before-after berupa foto cycle time',
            'Pastikan safety briefing dilakukan sebelum implementasi modifikasi tooling',
            'Data kaizen sudah ditinjau, silakan lanjutkan ke fase evaluasi Yamazumi',
            'Dibutuhkan koordinasi lanjutan dengan maintenance department untuk instalasi sensor',
        ];

        return [
            'activity_plan_id' => ActivityPlan::factory(),
            'admin_id' => UserAccount::factory()->admin(),
            'followup_note' => fake()->randomElement($notes),
            'status' => fake()->randomElement(['pending', 'in_review', 'resolved', 'escalated']),
        ];
    }
}

