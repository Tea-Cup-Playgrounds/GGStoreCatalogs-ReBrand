<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'V-Gen',      'slug' => 'v-gen'],
            ['name' => 'Sandisk',    'slug' => 'sandisk'],
            ['name' => 'Toshiba',    'slug' => 'toshiba'],
            ['name' => 'Samsung',    'slug' => 'samsung'],
            ['name' => 'Ezviz',      'slug' => 'ezviz'],
            ['name' => 'Thinkplus',  'slug' => 'thinkplus'],
            ['name' => 'Xiaomi',     'slug' => 'xiaomi'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['slug' => $brand['slug']], $brand);
        }
    }
}
