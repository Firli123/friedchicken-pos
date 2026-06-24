<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Ayam',     'slug' => 'ayam',     'sort_order' => 1],
            ['name' => 'Paket',    'slug' => 'paket',    'sort_order' => 2],
            ['name' => 'Tambahan', 'slug' => 'tambahan', 'sort_order' => 3],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
