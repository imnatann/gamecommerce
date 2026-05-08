<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'payment_method',
        'gateway',
        'gateway_transaction_id',
        'snap_token',
        'currency',
        'amount',
        'status',
        'gateway_response',
        'payload',
        'raw_notification',
        'paid_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => PaymentStatus::class,
            'gateway_response' => 'array',
            'payload' => 'array',
            'raw_notification' => 'array',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $payment) {
            $payment->syncMethodAliases();
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', PaymentStatus::SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::SUCCESS;
    }

    private function syncMethodAliases(): void
    {
        $method = $this->attributes['method'] ?? $this->attributes['payment_method'] ?? null;

        $this->attributes['method'] = $method;
        $this->attributes['payment_method'] = $method;
    }
}
