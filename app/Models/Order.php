<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'subtotal',
        'subtotal_amount',
        'discount',
        'total',
        'total_amount',
        'discount_amount',
        'final_amount',
        'voucher_id',
        'status',
        'notes',
        'ip_address',
        'expires_at',
        'paid_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'subtotal_amount' => 'integer',
            'discount' => 'integer',
            'total' => 'integer',
            'total_amount' => 'integer',
            'discount_amount' => 'integer',
            'final_amount' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => OrderStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'GC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
            }
        });

        static::saving(function (self $order) {
            $order->syncAmountAliases();
        });
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    public function scopeByStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', OrderStatus::PAID);
    }

    public function getNetAmountAttribute(): int
    {
        return $this->final_amount ?: max(0, $this->total_amount - $this->discount_amount);
    }

    private function syncAmountAliases(): void
    {
        $discount = (int) ($this->attributes['discount'] ?? $this->attributes['discount_amount'] ?? 0);
        $final = (int) ($this->attributes['final_amount'] ?? $this->attributes['total'] ?? $this->attributes['total_amount'] ?? 0);
        $subtotal = (int) ($this->attributes['subtotal'] ?? (
            array_key_exists('final_amount', $this->attributes)
                ? ($this->attributes['total_amount'] ?? $final + $discount)
                : $final + $discount
        ));

        $this->attributes['discount'] = $discount;
        $this->attributes['discount_amount'] = (int) ($this->attributes['discount_amount'] ?? $discount);
        $this->attributes['subtotal'] = $subtotal;
        $this->attributes['subtotal_amount'] = (int) ($this->attributes['subtotal_amount'] ?? $subtotal);
        $this->attributes['total'] = $final;
        $this->attributes['final_amount'] = $final;
        $this->attributes['total_amount'] = (int) ($this->attributes['total_amount'] ?? $subtotal);
    }
}
