<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Response;
use Vinkla\Hashids\Facades\Hashids;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products = Product::select('id', 'slug', 'updated_at')->get()
            ->map(fn ($p) => (object) [
                'hash' => Hashids::encode($p->id),
                'slug' => $p->slug,
                'updated_at' => $p->updated_at,
            ]);
        $brands = Brand::select('id', 'slug', 'updated_at')->get();

        $content = view('sitemap', compact('products', 'brands'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
