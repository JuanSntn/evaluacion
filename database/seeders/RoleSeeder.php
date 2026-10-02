<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->updateOrCreate(
            ['slug' => Role::ADMIN],
            ['name' => 'Administrador'],
        );

        Role::query()->updateOrCreate(
            ['slug' => Role::ACCOUNT_OWNER],
            ['name' => 'Dueño de cuenta'],
        );
    }
}
