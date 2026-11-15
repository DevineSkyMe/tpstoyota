<?php

namespace Database\Factories;

use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Division>
 */
class DivisionFactory extends Factory
{
    protected $model = Division::class;

    protected static int $divisionIndex = 0;

    protected static array $divisions = [
        'Production Division',
        'Quality Assurance & Control Division',
        'Manufacturing Engineering Division',
        'Human Resources & General Affairs Division',
        'Supply Chain & Logistics Division',
        'Technical & Maintenance Division',
    ];

    public function definition(): array
    {
        $name = self::$divisions[self::$divisionIndex % count(self::$divisions)] ?? fake()->unique()->jobTitle() . ' Division';
        self::$divisionIndex++;

        return [
            'name' => $name,
        ];
    }
}

