<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Vinkla\Hashids\Facades\Hashids;

class HomeController extends Controller
{
    public function index(): Response
    {
        $banners = Cache::remember('home:banners', now()->addMinutes(30), function () {
            return Banner::active()->get(['id', 'title', 'banner_image_url', 'redirect_url']);
        });

        $categories = Cache::remember('home:categories', now()->addMinutes(60), function () {
            return Category::withCount('products')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'category_photo']);
        });

        $promoProducts = Cache::remember('home:promo_products', now()->addMinutes(15), function () {
            return Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'brand'])
                ->whereNotNull('promo_price')
                ->where(function ($q) {
                    $q->whereNull('promo_price_start_date')
                        ->orWhere(function ($q) {
                            $q->where('promo_price_start_date', '<=', now())
                                ->where('promo_price_end_date', '>=', now());
                        });
                })
                ->latest()
                ->limit(10)
                ->get();
        });

        $newestProducts = Cache::remember('home:newest_products', now()->addMinutes(15), function () {
            return Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'brand'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        });

        return Inertia::render('Home', [
            'banners' => $banners,
            'categories' => $categories,
            'promoProducts' => $this->formatProducts($promoProducts),
            'newestProducts' => $this->formatProducts($newestProducts),
        ]);
    }

    private function formatProducts($products): array
    {
        return $products->map(fn (Product $p) => [
            'hash' => Hashids::encode($p->id),
            'name' => $p->name,
            'slug' => $p->slug,
            'price' => $p->price,
            'promo_price' => $p->promo_price,
            'is_promo_active' => $p->is_promo_active,
            'first_photo' => $p->photos->first()?->photo_url,
            'brand_name' => $p->brand?->name,
        ])->toArray();
    }
}
