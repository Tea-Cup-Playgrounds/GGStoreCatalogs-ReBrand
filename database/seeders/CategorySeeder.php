<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kartu SD',       'slug' => 'kartu-sd'],
            ['name' => 'Airbuds',         'slug' => 'airbuds'],
            ['name' => 'Earphone',        'slug' => 'earphone'],
            ['name' => 'Charger Type C',  'slug' => 'charger-type-c'],
            ['name' => 'Casing HP',       'slug' => 'casing-hp'],
            ['name' => 'Tempered Glass',  'slug' => 'tempered-glass'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
