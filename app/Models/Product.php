<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description',
        'brand_id', 'category_id',
        'price', 'promo_price', 'promo_price_start_date', 'promo_price_end_date',
        'produk_dilihat',
    ];

    protected function casts(): array
    {
        return [
            'promo_price_start_date' => 'datetime',
            'promo_price_end_date'   => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /** Returns true when a promo price is currently active. */
    public function getIsPromoActiveAttribute(): bool
    {
        if (! $this->promo_price) {
            return false;
        }

        $now = now();

        if ($this->promo_price_start_date && $this->promo_price_end_date) {
            return $now->between($this->promo_price_start_date, $this->promo_price_end_date);
        }

        return true;
    }

    /** The effective selling price (promo if active, otherwise regular). */
    public function getEffectivePriceAttribute(): int
    {
        return $this->is_promo_active ? $this->promo_price : $this->price;
    }

    /** First photo URL or null. */
    public function getFirstPhotoAttribute(): ?string
    {
        return $this->photos->first()?->photo_url;
    }
}
