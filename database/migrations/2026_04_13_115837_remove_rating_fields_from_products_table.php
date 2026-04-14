<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['avg_rating', 'total_raters']);
        });

        Schema::dropIfExists('ratings');
    }

    public function down(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->unsignedTinyInteger('rating');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['product_id', 'ip_address']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->float('avg_rating')->default(0)->after('total_sold');
            $table->unsignedInteger('total_raters')->default(0)->after('avg_rating');
        });
    }
};
