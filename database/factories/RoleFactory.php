<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'slug' => fake()->unique()->slug(2),
        ];
    }

    public function accountOwner(): static
    {
        return $this->state(fn (): array => [
            'name' => 'Dueño de cuenta',
            'slug' => Role::ACCOUNT_OWNER,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (): array => [
            'name' => 'Administrador',
            'slug' => Role::ADMIN,
        ]);
    }
}
