<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::create([
            'id' => Role::ADMIN,
            'name' => 'Admin',
            'description' => 'Usuario administrador',
        ]);

        Role::create([
            'id' => Role::TEACHER,
            'name' => 'Profesor',
            'description' => 'Usuario profesor',
        ]);

        Role::create([
            'id' => Role::STUDENT,
            'name' => 'Estudiante',
            'description' => 'Usuario estudiante',
        ]);

        $this->call(UserSeeder::class);
    }
}
