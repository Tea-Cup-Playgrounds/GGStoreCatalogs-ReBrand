<?php

use App\Http\Controllers\Admin\BannerAdminController;
use App\Http\Controllers\Admin\BrandAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/product/{hash}', [ProductController::class, 'show'])->name('product.show');
Route::get('/brands', [BrandController::class, 'index'])->name('brands');
Route::get('/wishlist', fn () => Inertia::render('Wishlist'))->name('wishlist');
Route::post('/api/wishlist/products', [WishlistController::class, 'products'])->name('wishlist.products');

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/upload', [UploadController::class, 'store'])->name('upload');

    Route::resource('products',   ProductAdminController::class)->except(['show'])->names('products');
    Route::resource('categories', CategoryAdminController::class)->except(['show', 'create', 'edit'])->names('categories');
    Route::resource('brands',     BrandAdminController::class)->except(['show', 'create', 'edit'])->names('brands');
    Route::resource('banners',    BannerAdminController::class)->except(['show', 'create', 'edit'])->names('banners');
});

// Legacy dashboard redirect for authenticated users
Route::get('dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
