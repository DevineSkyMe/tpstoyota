<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserAccount>
 */
class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['karyawan', 'admin', 'hrd', 'omdd', 'manager']),
            'employee_id' => Employee::factory(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function tlc(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'hrd',
        ]);
    }

    public function hrd(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'hrd',
        ]);
    }

    public function omdd(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'omdd',
        ]);
    }

    public function director(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'manager',
        ]);
    }

    public function participant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'karyawan',
        ]);
    }

    public function tpsg(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'karyawan',
        ]);
    }
}

