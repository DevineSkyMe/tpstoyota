<?php

namespace Database\Factories;

use App\Models\Plant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plant>
 */
class PlantFactory extends Factory
{
    protected $model = Plant::class;

    protected static int $plantIndex = 0;

    protected static array $plants = [
        'Plant 1 Sunter 1',
        'Plant 2 Karawang 1',
        'Plant 3 Karawang 2',
        'Plant 4 Sunter 2 (Casting)',
        'Plant 5 Karawang 3 (Engine)',
    ];

    public function definition(): array
    {
        $name = self::$plants[self::$plantIndex % count(self::$plants)] ?? fake()->unique()->company() . ' Plant';
        self::$plantIndex++;

        return [
            'name' => $name,
        ];
    }
}

