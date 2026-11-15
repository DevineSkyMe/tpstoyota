<?php

namespace Database\Factories;

use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopScoreReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkshopScoreReport>
 */
class WorkshopScoreReportFactory extends Factory
{
    protected $model = WorkshopScoreReport::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['generated', 'sent_hrd', 'sent_director', 'completed']);

        $sentHrd = in_array($status, ['sent_hrd', 'sent_director', 'completed']) ? now()->subDays(2) : null;
        $sentDirector = in_array($status, ['sent_director', 'completed']) ? now()->subDay() : null;

        return [
            'workshop_id' => Workshop::factory(),
            'generated_by' => UserAccount::factory()->admin(),
            'pdf_path' => 'reports/score_reports/ws_report_' . fake()->uuid() . '.pdf',
            'generated_at' => now()->subDays(3),
            'sent_to_hrd_at' => $sentHrd,
            'sent_to_director_at' => $sentDirector,
            'status' => $status,
        ];
    }
}

