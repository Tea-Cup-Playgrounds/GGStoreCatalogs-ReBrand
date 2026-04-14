<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $kartuSd   = Category::where('slug', 'kartu-sd')->first();
        $airbuds   = Category::where('slug', 'airbuds')->first();
        $charger   = Category::where('slug', 'charger-type-c')->first();
        $casing    = Category::where('slug', 'casing-hp')->first();
        $tempered  = Category::where('slug', 'tempered-glass')->first();

        $vgen      = Brand::where('slug', 'v-gen')->first();
        $sandisk   = Brand::where('slug', 'sandisk')->first();
        $samsung   = Brand::where('slug', 'samsung')->first();
        $thinkplus = Brand::where('slug', 'thinkplus')->first();
        $xiaomi    = Brand::where('slug', 'xiaomi')->first();

        $products = [
            [
                'name'        => 'Micro SD V-GeN Turbo 128GB 100MB/S',
                'description' => 'Kartu Micro SDHC UHS-1 U1. Kecepatan baca hingga 100MB/s. Cocok untuk smartphone, tablet, dan action camera.',
                'brand_id'    => $vgen?->id,
                'category_id' => $kartuSd?->id,
                'price'       => 138000,
                'variants'    => ['Non Adapter', 'With Adapter'],
                'photo'       => 'https://placehold.co/600x600/e6b120/ffffff?text=V-Gen+128GB',
            ],
            [
                'name'        => 'SanDisk microSDXC Ultra 128GB 100MB/s',
                'description' => 'Class 10 UHS-I. Kecepatan hingga 100MB/s. Garansi resmi 7 tahun Sandisk Indonesia.',
                'brand_id'    => $sandisk?->id,
                'category_id' => $kartuSd?->id,
                'price'       => 165000,
                'variants'    => [],
                'photo'       => 'https://placehold.co/600x600/000000/ffffff?text=SanDisk+128GB',
            ],
            [
                'name'        => 'Samsung microSD EVO Plus 128GB',
                'description' => 'microSDXC UHS-I. Tersedia dalam 64GB hingga 1TB. Garansi resmi Samsung.',
                'brand_id'    => $samsung?->id,
                'category_id' => $kartuSd?->id,
                'price'       => 185000,
                'variants'    => ['64GB', '128GB', '256GB', '512GB', '1TB'],
                'photo'       => 'https://placehold.co/600x600/1428a0/ffffff?text=Samsung+EVO+Plus',
            ],
            [
                'name'        => 'Samsung Galaxy Buds2 Pro Bluetooth',
                'description' => '24 Bit Hi-Fi Audio, Intelligent ANC, IPX7, BT 5.3. Garansi resmi Samsung Indonesia.',
                'brand_id'    => $samsung?->id,
                'category_id' => $airbuds?->id,
                'price'       => 1349000,
                'variants'    => ['Bora Purple', 'Graphite', 'White'],
                'photo'       => 'https://placehold.co/600x600/6b21a8/ffffff?text=Galaxy+Buds2+Pro',
            ],
            [
                'name'        => 'Lenovo Thinkplus LP1 TWS Earphone Bluetooth HiFi',
                'description' => 'TWS Earphone stereo HiFi, Bluetooth 5.3, low latency, baterai 5 jam, input Type-C.',
                'brand_id'    => $thinkplus?->id,
                'category_id' => $airbuds?->id,
                'price'       => 79000,
                'variants'    => ['Hitam', 'Putih'],
                'photo'       => 'https://placehold.co/600x600/1b1b18/ffffff?text=Thinkplus+LP1',
            ],
            [
                'name'        => 'Xiaomi Redmi Buds 6 Play TWS Bluetooth 5.4',
                'description' => '5 mode EQ Preset, ANC, baterai 7.5 jam, total 36 jam dengan case, port Type-C.',
                'brand_id'    => $xiaomi?->id,
                'category_id' => $airbuds?->id,
                'price'       => 129000,
                'variants'    => ['Black', 'White', 'Blue', 'Pink'],
                'photo'       => 'https://placehold.co/600x600/ff6900/ffffff?text=Redmi+Buds+6+Play',
            ],
            [
                'name'        => 'Xiaomi Redmi Buds 6 Pro ANC 55dB Hi-Res LDAC',
                'description' => 'Triple-driver coaxial, ANC 55dB, Hi-Res Audio LDAC, Bluetooth 5.3, baterai 36 jam.',
                'brand_id'    => $xiaomi?->id,
                'category_id' => $airbuds?->id,
                'price'       => 999000,
                'variants'    => ['Space Black', 'Glacier White', 'Lavender Purple'],
                'photo'       => 'https://placehold.co/600x600/0f172a/ffffff?text=Redmi+Buds+6+Pro',
            ],
            [
                'name'        => 'Charger Xiaomi 33W Original Fast Charging Type C',
                'description' => 'Fast charging 33W, multiple protection technology, kompatibel Android & Apple.',
                'brand_id'    => $xiaomi?->id,
                'category_id' => $charger?->id,
                'price'       => 28800,
                'variants'    => [],
                'photo'       => 'https://placehold.co/600x600/e6b120/000000?text=Charger+33W',
            ],
            [
                'name'        => 'Casing HP Soft Case Redmi / POCO Series',
                'description' => 'Soft case bahan TPU, cocok untuk berbagai tipe Redmi dan POCO. Tersedia banyak pilihan tipe.',
                'brand_id'    => null,
                'category_id' => $casing?->id,
                'price'       => 43680,
                'variants'    => [],
                'photo'       => 'https://placehold.co/600x600/64748b/ffffff?text=Soft+Case',
            ],
            [
                'name'        => 'Tempered Glass Bening 0.3mm Anti Gores',
                'description' => 'Tersedia untuk ratusan tipe HP Samsung, OPPO, Realme, Xiaomi, Vivo. Kualitas grosir.',
                'brand_id'    => null,
                'category_id' => $tempered?->id,
                'price'       => 1200,
                'variants'    => [],
                'photo'       => 'https://placehold.co/600x600/e2e8f0/1b1b18?text=Tempered+Glass',
            ],
        ];

        foreach ($products as $data) {
            $variants = $data['variants'];
            $photo    = $data['photo'];
            unset($data['variants'], $data['photo']);

            $data['slug'] = Str::slug($data['name']);

            $product = Product::firstOrCreate(['slug' => $data['slug']], $data);

            if ($product->wasRecentlyCreated) {
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'photo_url'  => $photo,
                    'sort_order' => 0,
                ]);

                foreach ($variants as $variant) {
                    ProductVariant::create([
                        'product_id'   => $product->id,
                        'variant_name' => $variant,
                    ]);
                }
            }
        }
    }
}
