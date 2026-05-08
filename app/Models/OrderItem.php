<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'quantity',
        'price',
        'total',
        'server',
        'region',
        'server_info',
        'delivery_type',
        'delivery_status',
        'delivery_data',
        'status',
        'delivered_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'integer',
            'total' => 'integer',
            'delivery_data' => 'array',
            'status' => OrderStatus::class,
            'delivered_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->hydrateProductSnapshot();
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->total ?: $this->price * $this->quantity;
    }

    private function hydrateProductSnapshot(): void
    {
        $this->attributes['total'] = (int) ($this->attributes['total'] ?? 0);

        if ($this->attributes['total'] === 0 && isset($this->attributes['price'], $this->attributes['quantity'])) {
            $this->attributes['total'] = (int) $this->attributes['price'] * (int) $this->attributes['quantity'];
        }

        if (empty($this->attributes['seller_id']) || empty($this->attributes['delivery_type'])) {
            $product = $this->product ?: Product::find($this->attributes['product_id'] ?? null);

            if ($product) {
                $this->attributes['seller_id'] ??= $product->seller_id;
                $this->attributes['delivery_type'] ??= $product->delivery_type instanceof \BackedEnum
                    ? $product->delivery_type->value
                    : $product->delivery_type;
            }
        }

        if (empty($this->attributes['server_info'])) {
            $this->attributes['server_info'] = $this->attributes['server'] ?? null;
        }
    }
}
