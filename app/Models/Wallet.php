<?php

namespace App\Models;

use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'balance',
        'available_balance',
        'frozen_amount',
        'is_locked',
        'locked_reason',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'available_balance' => 'integer',
            'frozen_amount' => 'integer',
            'is_locked' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function deposit(int $amount, string $description = null, string $referenceType = null, int $referenceId = null): WalletTransaction
    {
        $before = $this->balance;
        $this->increment('balance', $amount);
        $this->increment('available_balance', $amount);

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'type' => WalletTransactionType::DEPOSIT->value,
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function withdraw(int $amount, string $description = null, string $referenceType = null, int $referenceId = null): WalletTransaction
    {
        $before = $this->balance;
        $this->decrement('balance', $amount);
        $this->decrement('available_balance', $amount);

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'type' => WalletTransactionType::WITHDRAWAL->value,
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function hasSufficientBalance(int $amount): bool
    {
        return $this->available_balance >= $amount;
    }

    public function getPendingBalanceAttribute(): int
    {
        return $this->available_balance;
    }

    public function getFrozenBalanceAttribute(): int
    {
        return $this->frozen_amount;
    }
}
