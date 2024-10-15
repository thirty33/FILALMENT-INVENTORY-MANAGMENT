<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa de buen precio'
        ]);
        Category::create([
            'name' => 'Accesorios',
            'description' => 'Ropa de buen precio'
        ]);
        Category::create([
            'name' => 'Juguetes',
            'description' => 'Ropa de buen precio'
        ]);
        Category::create([
            'name' => 'Alimentos',
            'description' => 'Ropa de buen precio'
        ]);
    }
}
