<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'related_wallet_id',
        'order_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $transaction) {
            if (empty($transaction->wallet_id) && $transaction->user_id) {
                $transaction->wallet_id = Wallet::firstOrCreate(
                    ['user_id' => $transaction->user_id],
                    ['balance' => 0, 'available_balance' => 0, 'frozen_amount' => 0]
                )->id;
            }

            if (empty($transaction->user_id) && $transaction->wallet_id) {
                $transaction->user_id = Wallet::find($transaction->wallet_id)?->user_id;
            }
        });
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'related_wallet_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isCredit(): bool
    {
        return in_array($this->type, ['in', 'deposit', 'transfer_in', 'refund', 'earning', 'unfreeze'], true);
    }

    public function isDebit(): bool
    {
        return in_array($this->type, ['out', 'withdrawal', 'transfer_out', 'payment', 'freeze'], true);
    }
}
