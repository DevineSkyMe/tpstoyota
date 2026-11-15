<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Plant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $levels = ['KP1', 'KP2', 'KP3', 'KP4'];
        $currentLevel = fake()->randomElement($levels);
        $previousLevel = match ($currentLevel) {
            'KP4' => 'KP3',
            'KP3' => 'KP2',
            'KP2' => 'KP1',
            default => null,
        };

        $positions = [
            'Team Member',
            'Team Leader',
            'Group Leader',
            'Foreman',
            'Supervisor',
            'Section Head',
            'TPS Coordinator',
        ];

        $themes = [
            'Reduksi Karakuri Muda di Line Assembly Final',
            'Optimasi Standardized Work & Yamazumi Chart di Welding',
            'Penerapan Andon Digital & Quick Response Stamping Line',
            'Eliminasi Bottleneck dengan Konsep Heijunka di Engine Plant',
            'Implementasi Poka-Yoke Sensor Presisi pada Jig Welding',
            'Efisiensi Material Handling AGV berbasis Pull System Kanban',
        ];

        $activityTypes = ['Kaizen Project', 'QCC', 'Jishuken', 'Suggestion System', 'Standardized Work Improvement'];

        $nik = (string) fake()->unique()->numberBetween(100000, 999999);

        return [
            'name' => fake()->name(),
            'username' => $nik,
            'birth_date' => fake()->dateTimeBetween('-45 years', '-22 years')->format('Y-m-d'),
            'position' => fake()->randomElement($positions),
            'photo' => 'uploads/photos/photo_' . $nik . '.jpg',
            'certificate' => 'uploads/certificates/cert_' . $nik . '.pdf',
            'current_tps_level' => $currentLevel,
            'previous_tps_level' => $previousLevel,
            'tahun_lulus_terakhir' => (string) fake()->numberBetween(2022, 2024),
            'tahun_lulus_saat_ini' => (string) fake()->numberBetween(2025, 2026),
            'last_activity_theme' => fake()->randomElement($themes),
            'last_activity_type' => fake()->randomElement($activityTypes),
            'batch' => 'Batch ' . fake()->numberBetween(1, 4) . ' ' . date('Y'),
            'registration_year' => (int) date('Y'),
            'status' => 'active',
            'plant_id' => Plant::factory(),
            'division_id' => Division::factory(),
            'department_id' => Department::factory(),
        ];
    }
}

