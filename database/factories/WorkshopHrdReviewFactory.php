<?php

namespace Database\Factories;

use App\Models\UserAccount;
use App\Models\WorkshopCandidate;
use App\Models\WorkshopHrdReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkshopHrdReview>
 */
class WorkshopHrdReviewFactory extends Factory
{
    protected $model = WorkshopHrdReview::class;

    public function definition(): array
    {
        $notes = [
            'Disetujui HRD, persyaratan masa kerja dan evaluasi kinerja tahunan terpenuhi.',
            'Direkomendasikan ikut workshop batch ini, telah lolos pra-kualifikasi TPS dasar.',
            'Penundaan direkomendasikan karena bentrok dengan jadwal audit mutu division.',
            'Lolos verifikasi data kepegawaian dan sertifikasi level TPS sebelumnya valid.',
        ];

        $status = fake()->randomElement(['pending', 'recommended', 'rejected']);

        return [
            'candidate_id' => WorkshopCandidate::factory(),
            'hrd_id' => UserAccount::factory()->hrd(),
            'hrd_status' => $status,
            'hrd_note' => fake()->randomElement($notes),
            'reviewed_at' => $status !== 'pending' ? now() : null,
        ];
    }
}

