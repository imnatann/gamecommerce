<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('method')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('gateway');
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->text('snap_token')->nullable();
            $table->string('currency', 3)->default('IDR');
            $table->unsignedBigInteger('amount');
            $table->enum('status', ['pending', 'success', 'failed', 'expired', 'refunded'])
                ->default('pending')
                ->index();
            $table->json('gateway_response')->nullable();
            $table->json('payload')->nullable();
            $table->json('raw_notification')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
