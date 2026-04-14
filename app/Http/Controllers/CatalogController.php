<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with([
            'photos' => fn ($q) => $q->orderBy('sort_order')->limit(1),
            'brand',
            'category',
        ]);

        // Search
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Category filter
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        // Brand filter
        if ($brandSlug = $request->input('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brandSlug));
        }

        // Promo filter
        if ($request->boolean('promo')) {
            $query->whereNotNull('promo_price')
                ->where(function ($q) {
                    $q->whereNull('promo_price_start_date')
                        ->orWhere(function ($q) {
                            $q->where('promo_price_start_date', '<=', now())
                                ->where('promo_price_end_date', '>=', now());
                        });
                });
        }

        // Sort
        match ($request->input('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->orderByDesc('created_at'),
        };

        $products = $query->paginate(20)->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
        $brands     = Brand::orderBy('name')->get(['id', 'name', 'slug']);

        return Inertia::render('Catalog', [
            'products'   => $products->through(fn (Product $p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'slug'            => $p->slug,
                'price'           => $p->price,
                'promo_price'     => $p->promo_price,
                'is_promo_active' => $p->is_promo_active,
                'first_photo'     => $p->photos->first()?->photo_url,
                'brand_name'      => $p->brand?->name,
                'category_name'   => $p->category?->name,
            ]),
            'categories' => $categories,
            'brands'     => $brands,
            'filters'    => $request->only(['search', 'category', 'brand', 'sort', 'promo']),
        ]);
    }
}
