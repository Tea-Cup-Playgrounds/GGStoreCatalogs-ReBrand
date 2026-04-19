<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;
use Vinkla\Hashids\Facades\Hashids;

class HomeController extends Controller
{
    public function index(): Response
    {
        $banners = Banner::active()->get(['id', 'title', 'banner_image_url', 'redirect_url']);

        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category_photo']);

        $promoProducts = Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'brand'])
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

        $newestProducts = Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'brand'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

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
