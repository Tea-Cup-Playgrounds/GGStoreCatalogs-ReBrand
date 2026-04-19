<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalProducts  = Product::count();
        $totalCategories = Category::count();
        $totalBrands    = Brand::count();
        $totalUsers     = User::count();

        $promoProducts = Product::whereNotNull('promo_price')
            ->where(function ($q) {
                $q->whereNull('promo_price_start_date')
                    ->orWhere(function ($q) {
                        $q->where('promo_price_start_date', '<=', now())
                            ->where('promo_price_end_date', '>=', now());
                    });
            })
            ->count();

        // Top 5 most viewed products
        $topViewed = Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'brand', 'category'])
            ->orderByDesc('produk_dilihat')
            ->limit(5)
            ->get()
            ->map(fn (Product $p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'produk_dilihat' => $p->produk_dilihat ?? 0,
                'price'         => $p->price,
                'first_photo'   => $p->photos->first()?->photo_url,
                'brand_name'    => $p->brand?->name,
                'category_name' => $p->category?->name,
            ]);

        // Products per category
        $categoryStats = Category::withCount('products')
            ->orderByDesc('products_count')
            ->get(['id', 'name', 'products_count'])
            ->map(fn ($c) => [
                'name'  => $c->name,
                'count' => $c->products_count,
            ]);

        // Recently added products
        $recentProducts = Product::with(['brand', 'category'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Product $p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'price'         => $p->price,
                'brand_name'    => $p->brand?->name,
                'category_name' => $p->category?->name,
                'created_at'    => $p->created_at->diffForHumans(),
            ]);

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'total_products'   => $totalProducts,
                'total_categories' => $totalCategories,
                'total_brands'     => $totalBrands,
                'total_users'      => $totalUsers,
                'promo_products'   => $promoProducts,
            ],
            'topViewed'      => $topViewed,
            'categoryStats'  => $categoryStats,
            'recentProducts' => $recentProducts,
        ]);
    }
}
