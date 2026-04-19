<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductAdminController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with([
            'photos' => fn ($q) => $q->orderBy('sort_order')->limit(1),
            'brand',
            'category',
        ]);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        if ($brand = $request->input('brand')) {
            $query->where('brand_id', $brand);
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('admin/products/Index', [
            'products'   => $products->through(fn (Product $p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'price'         => $p->price,
                'promo_price'   => $p->promo_price,
                'is_promo_active' => $p->is_promo_active,
                'first_photo'   => $p->photos->first()?->photo_url,
                'brand_name'    => $p->brand?->name,
                'category_name' => $p->category?->name,
                'produk_dilihat' => $p->produk_dilihat ?? 0,
            ]),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands'     => Brand::orderBy('name')->get(['id', 'name']),
            'filters'    => $request->only(['search', 'category', 'brand']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/products/Form', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands'     => Brand::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:255',
            'slug'                   => 'nullable|string|max:255|unique:products,slug',
            'description'            => 'nullable|string',
            'brand_id'               => 'nullable|exists:brands,id',
            'category_id'            => 'nullable|exists:categories,id',
            'price'                  => 'required|integer|min:0',
            'promo_price'            => 'nullable|integer|min:0',
            'promo_price_start_date' => 'nullable|date',
            'promo_price_end_date'   => 'nullable|date|after_or_equal:promo_price_start_date',
            'variants'               => 'nullable|array',
            'variants.*'             => 'string|max:100',
            'photos'                 => 'nullable|array',
            'photos.*'               => 'string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $product = Product::create($data);

        foreach (($data['variants'] ?? []) as $variant) {
            if (trim($variant)) {
                ProductVariant::create(['product_id' => $product->id, 'variant_name' => trim($variant)]);
            }
        }

        foreach (($data['photos'] ?? []) as $i => $url) {
            if (trim($url)) {
                ProductPhoto::create(['product_id' => $product->id, 'photo_url' => trim($url), 'sort_order' => $i]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): Response
    {
        $product->load(['photos' => fn ($q) => $q->orderBy('sort_order'), 'variants']);

        return Inertia::render('admin/products/Form', [
            'product'    => [
                'id'                     => $product->id,
                'name'                   => $product->name,
                'slug'                   => $product->slug,
                'description'            => $product->description,
                'brand_id'               => $product->brand_id,
                'category_id'            => $product->category_id,
                'price'                  => $product->price,
                'promo_price'            => $product->promo_price,
                'promo_price_start_date' => $product->promo_price_start_date?->toDateString(),
                'promo_price_end_date'   => $product->promo_price_end_date?->toDateString(),
                'photos'                 => $product->photos->map(fn ($ph) => ['id' => $ph->id, 'photo_url' => $ph->photo_url])->toArray(),
                'variants'               => $product->variants->pluck('variant_name')->toArray(),
            ],
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands'     => Brand::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:255',
            'slug'                   => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description'            => 'nullable|string',
            'brand_id'               => 'nullable|exists:brands,id',
            'category_id'            => 'nullable|exists:categories,id',
            'price'                  => 'required|integer|min:0',
            'promo_price'            => 'nullable|integer|min:0',
            'promo_price_start_date' => 'nullable|date',
            'promo_price_end_date'   => 'nullable|date|after_or_equal:promo_price_start_date',
            'variants'               => 'nullable|array',
            'variants.*'             => 'string|max:100',
            'photos'                 => 'nullable|array',
            'photos.*'               => 'string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $product->update($data);

        // Sync variants
        $product->variants()->delete();
        foreach (($data['variants'] ?? []) as $variant) {
            if (trim($variant)) {
                ProductVariant::create(['product_id' => $product->id, 'variant_name' => trim($variant)]);
            }
        }

        // Sync photos
        $product->photos()->delete();
        foreach (($data['photos'] ?? []) as $i => $url) {
            if (trim($url)) {
                ProductPhoto::create(['product_id' => $product->id, 'photo_url' => trim($url), 'sort_order' => $i]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
