<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryAdminController extends Controller
{
    public function index(): Response
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return Inertia::render('admin/categories/Index', [
            'categories' => $categories->map(fn (Category $c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'slug'           => $c->slug,
                'category_photo' => $c->category_photo,
                'products_count' => $c->products_count,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255|unique:categories,name',
            'slug'           => 'nullable|string|max:255|unique:categories,slug',
            'category_photo' => 'nullable|string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Category::create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug'           => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'category_photo' => 'nullable|string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $category->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
