<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\UserAccount;
use App\Models\WorkshopCandidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkshopCandidate>
 */
class WorkshopCandidateFactory extends Factory
{
    protected $model = WorkshopCandidate::class;

    public function definition(): array
    {
        $reasons = [
            'Memiliki rekam jejak kaizen yang konsisten dan pemahaman genba yang kuat.',
            'Telah menyelesaikan proyek standard work improvement dengan reduksi cycle time 15%.',
            'Direkomendasikan untuk naik ke level TPS selanjutnya karena kepemimpinan dalam QCC.',
            'Menunjukkan kemampuan problem solving A3 yang sangat baik di area stamping.',
        ];

        $status = fake()->randomElement(['pending', 'recommended', 'rejected']);

        return [
            'employee_id' => Employee::factory(),
            'recommended_by' => UserAccount::factory()->tlc(),
            'target_tps_level' => fake()->randomElement(['KP3', 'KP4']),
            'tlc_reason' => fake()->randomElement($reasons),
            'tlc_status' => $status,
            'tlc_reviewed_at' => $status !== 'pending' ? now() : null,
        ];
    }
}

