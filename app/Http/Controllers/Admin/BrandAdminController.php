<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BrandAdminController extends Controller
{
    public function index(): Response
    {
        $brands = Brand::withCount('products')->orderBy('name')->get();

        return Inertia::render('admin/brands/Index', [
            'brands' => $brands->map(fn (Brand $b) => [
                'id'             => $b->id,
                'name'           => $b->name,
                'slug'           => $b->slug,
                'brand_photo'    => $b->brand_photo,
                'products_count' => $b->products_count,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name',
            'slug'        => 'nullable|string|max:255|unique:brands,slug',
            'brand_photo' => 'nullable|string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Brand::create($data);

        return back()->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug'        => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'brand_photo' => 'nullable|string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $brand->update($data);

        return back()->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return back()->with('success', 'Brand berhasil dihapus.');
    }
}
