<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('promo_price')->nullable();
            $table->timestamp('promo_price_start_date')->nullable();
            $table->timestamp('promo_price_end_date')->nullable();
            $table->unsignedInteger('total_sold')->default(0);
            $table->float('avg_rating')->default(0);
            $table->unsignedInteger('total_raters')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
