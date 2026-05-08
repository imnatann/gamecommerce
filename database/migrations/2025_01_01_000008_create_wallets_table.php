<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('currency', 3)->default('IDR');
            $table->unsignedBigInteger('balance')->default(0);
            $table->unsignedBigInteger('available_balance')->default(0);
            $table->unsignedBigInteger('frozen_amount')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->string('locked_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
