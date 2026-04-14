<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'            => 'Promo Kartu SD Terbaik',
                'banner_image_url' => 'https://placehold.co/1200x400/e6b120/ffffff?text=Promo+Kartu+SD',
                'redirect_url'     => '/catalog?category=kartu-sd',
                'is_active'        => true,
                'sort_order'       => 1,
            ],
            [
                'title'            => 'Earphone & Airbuds Murah',
                'banner_image_url' => 'https://placehold.co/1200x400/000000/e6b120?text=Earphone+%26+Airbuds',
                'redirect_url'     => '/catalog?category=airbuds',
                'is_active'        => true,
                'sort_order'       => 2,
            ],
            [
                'title'            => 'Charger & Aksesoris HP',
                'banner_image_url' => 'https://placehold.co/1200x400/1b1b18/ffffff?text=Charger+%26+Aksesoris',
                'redirect_url'     => '/catalog',
                'is_active'        => true,
                'sort_order'       => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(['title' => $banner['title']], $banner);
        }
    }
}
