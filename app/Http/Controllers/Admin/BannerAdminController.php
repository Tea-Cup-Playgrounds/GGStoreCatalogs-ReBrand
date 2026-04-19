<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Vinkla\Hashids\Facades\Hashids;

class BannerAdminController extends Controller
{
    public function index(): Response
    {
        $banners = Banner::orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('admin/banners/Index', [
            'banners'    => $banners->map(fn (Banner $b) => [
                'id'               => $b->id,
                'title'            => $b->title,
                'banner_image_url' => $b->banner_image_url,
                'redirect_url'     => $b->redirect_url,
                'is_active'        => $b->is_active,
                'sort_order'       => $b->sort_order,
            ]),
            'products'   => Product::orderBy('name')->get(['id', 'name'])->map(fn ($p) => [
                'id'   => $p->id,
                'hash' => Hashids::encode($p->id),
                'name' => $p->name,
            ]),
            'categories' => Category::orderBy('name')->get(['id', 'name', 'slug']),
            'brands'     => Brand::orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'banner_image_url' => 'required|string|max:500',
            'redirect_url'     => 'nullable|string|max:500',
            'is_active'        => 'boolean',
            'sort_order'       => 'integer|min:0',
        ]);

        Banner::create($data);

        return back()->with('success', 'Banner berhasil ditambahkan.');
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'banner_image_url' => 'required|string|max:500',
            'redirect_url'     => 'nullable|string|max:500',
            'is_active'        => 'boolean',
            'sort_order'       => 'integer|min:0',
        ]);

        $banner->update($data);

        return back()->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        // Delete stored image if it's a local storage URL
        if (str_contains($banner->banner_image_url, '/storage/uploads/')) {
            $path = str_replace('/storage/', '', parse_url($banner->banner_image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($path);
        }

        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus.');
    }
}
