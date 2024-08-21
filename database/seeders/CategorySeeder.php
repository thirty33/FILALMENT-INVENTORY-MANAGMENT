<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa a buen precio y de calidad',
        ]);

        Category::create([
            'name' => 'Accesorios',
            'description' => 'Accesorios para teléfonos, computadoras, etc.',
        ]);

        Category::create([
            'name' => 'Juguetes',
            'description' => 'Juguetes para niños y niñas',
        ]);

        Category::create([
            'name' => 'Alimentos',
            'description' => 'Alimentos con grandes descuentos y de calidad',
        ]);
    }
}
