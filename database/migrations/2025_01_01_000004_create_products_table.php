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
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('game_product_id')->nullable()->constrained('game_products')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->nullable()->index();
            $table->string('product_type')->nullable()->index();
            $table->unsignedBigInteger('price')->index();
            $table->unsignedBigInteger('original_price')->nullable();
            $table->unsignedInteger('stock')->nullable()->index();
            $table->string('server')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->enum('delivery_type', ['instant', 'manual', 'login'])->default('instant');
            $table->json('delivery_data')->nullable();
            $table->json('required_info')->nullable();
            $table->json('server_data')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_hot_deal')->default(false)->index();
            $table->unsignedInteger('sold_count')->default(0)->index();
            $table->decimal('rating', 3, 2)->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['game_id', 'is_active', 'price']);
            $table->index(['category_id', 'is_active']);
            $table->index(['game_product_id', 'is_active', 'price']);
            $table->index(['seller_id', 'is_active']);
            $table->index(['is_active', 'sold_count']);
            $table->index(['is_active', 'avg_rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
