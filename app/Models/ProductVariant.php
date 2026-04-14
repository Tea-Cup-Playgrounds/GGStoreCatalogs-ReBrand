<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'variant_name'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
