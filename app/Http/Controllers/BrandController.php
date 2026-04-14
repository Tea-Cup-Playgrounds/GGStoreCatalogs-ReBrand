<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function index(): Response
    {
        $brands = Brand::withCount('products')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'brand_photo']);

        return Inertia::render('Brands', [
            'brands' => $brands->map(fn (Brand $b) => [
                'id'             => $b->id,
                'name'           => $b->name,
                'slug'           => $b->slug,
                'brand_photo'    => $b->brand_photo,
                'products_count' => $b->products_count,
            ])->toArray(),
        ]);
    }
}
