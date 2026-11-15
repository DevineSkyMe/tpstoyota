<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    protected static int $deptIndex = 0;

    protected static array $departments = [
        'Press Department',
        'Welding Department',
        'Paint Department',
        'Assembly Department',
        'Quality Inspection Department',
        'Kaizen & TPS Promotion Department',
        'HRD & Training Center',
        'OMDD (Operations Management Development Division)',
        'Logistics & Material Handling Department',
        'Maintenance & Facility Department',
    ];

    public function definition(): array
    {
        $name = self::$departments[self::$deptIndex % count(self::$departments)] ?? fake()->unique()->word() . ' Department';
        self::$deptIndex++;

        return [
            'name' => $name,
        ];
    }
}

