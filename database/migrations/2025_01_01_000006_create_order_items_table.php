<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('total')->default(0);
            $table->string('server')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->string('server_info')->nullable();
            $table->string('delivery_type')->nullable()->index();
            $table->enum('delivery_status', ['pending', 'processing', 'delivered', 'rejected', 'failed'])
                ->default('pending')
                ->index();
            $table->json('delivery_data')->nullable();
            $table->enum('status', ['pending', 'paid', 'processing', 'delivered', 'completed', 'cancelled', 'refunded', 'disputed'])
                ->default('pending')
                ->index();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['delivery_status', 'delivered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
