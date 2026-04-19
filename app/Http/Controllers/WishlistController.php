<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class WishlistController extends Controller
{
    /**
     * Resolve an array of hashes to product data.
     * POST /api/wishlist/products  { hashes: string[] }
     */
    public function products(Request $request): JsonResponse
    {
        $hashes = $request->input('hashes', []);

        if (empty($hashes) || ! is_array($hashes)) {
            return response()->json([]);
        }

        // Decode all hashes to IDs, filtering out any invalid ones
        $ids = collect($hashes)
            ->map(fn ($h) => Hashids::decode($h)[0] ?? null)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([]);
        }

        $products = Product::with(['photos' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'brand'])
            ->whereIn('id', $ids)
            ->get();

        // Return in the same order as the input hashes
        $indexed = $products->keyBy('id');

        $result = $ids->map(function ($id) use ($indexed) {
            $p = $indexed->get($id);
            if (! $p) {
                return null;
            }

            return [
                'hash' => Hashids::encode($p->id),
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'promo_price' => $p->promo_price,
                'is_promo_active' => $p->is_promo_active,
                'first_photo' => $p->photos->first()?->photo_url,
                'brand_name' => $p->brand?->name,
            ];
        })->filter()->values();

        return response()->json($result);
    }
}
