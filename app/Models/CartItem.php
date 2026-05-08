<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'server',
        'region',
        'server_info',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if (empty($item->price) && $item->product_id) {
                $item->price = Product::find($item->product_id)?->price ?? 0;
            }

            if (empty($item->server_info)) {
                $item->server_info = $item->server;
            }
        });
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): int
    {
        return ($this->price ?: $this->product->price) * $this->quantity;
    }
}
