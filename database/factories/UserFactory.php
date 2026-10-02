<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => Role::factory()->accountOwner(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'rfc' => fake()->unique()->regexify('[A-Z]{4}[0-9]{6}[A-Z0-9]{3}'),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
