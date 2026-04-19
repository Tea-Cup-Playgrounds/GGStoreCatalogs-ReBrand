<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;
use Vinkla\Hashids\Facades\Hashids;

class ProductController extends Controller
{
    public function show(string $hash): Response
    {
        $decoded = Hashids::decode($hash);

        if (empty($decoded)) {
            abort(404);
        }

        $product = Product::with(['photos', 'variants', 'brand', 'category'])
            ->findOrFail($decoded[0]);

        // Increment view counter — no deduplication, every visit counts
        $product->increment('produk_dilihat');

        $related = Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1)])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(5)
            ->get();

        return Inertia::render('ProductDetail', [
            'product' => [
                'id' => $product->id,
                'hash' => Hashids::encode($product->id),
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'promo_price' => $product->promo_price,
                'is_promo_active' => $product->is_promo_active,
                'promo_price_start_date' => $product->promo_price_start_date?->toDateString(),
                'promo_price_end_date' => $product->promo_price_end_date?->toDateString(),
                'produk_dilihat' => $product->produk_dilihat,
                'brand_name' => $product->brand?->name,
                'brand_slug' => $product->brand?->slug,
                'category_name' => $product->category?->name,
                'category_slug' => $product->category?->slug,
                'photos' => $product->photos->map(fn ($ph) => [
                    'id' => $ph->id,
                    'photo_url' => $ph->photo_url,
                ])->toArray(),
                'variants' => $product->variants->pluck('variant_name')->toArray(),
            ],
            'related' => $related->map(fn (Product $p) => [
                'hash' => Hashids::encode($p->id),
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'promo_price' => $p->promo_price,
                'is_promo_active' => $p->is_promo_active,
                'first_photo' => $p->photos->first()?->photo_url,
                'brand_name' => $p->brand?->name,
            ])->toArray(),
        ]);
    }
}
