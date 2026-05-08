<?php

namespace App\Models;

use App\Enums\DeliveryType;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'game_id',
        'category_id',
        'game_product_id',
        'name',
        'slug',
        'description',
        'type',
        'product_type',
        'price',
        'original_price',
        'stock',
        'server',
        'region',
        'delivery_type',
        'delivery_data',
        'required_info',
        'server_data',
        'is_active',
        'is_featured',
        'is_hot_deal',
        'sold_count',
        'rating',
        'avg_rating',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'original_price' => 'integer',
            'stock' => 'integer',
            'sold_count' => 'integer',
            'rating' => 'decimal:2',
            'avg_rating' => 'decimal:2',
            'rating_count' => 'integer',
            'delivery_type' => DeliveryType::class,
            'delivery_data' => 'array',
            'required_info' => 'array',
            'server_data' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_hot_deal' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(6);
            }
        });

        static::saving(function (self $product) {
            $product->syncDenormalizedCatalogFields();
            $product->syncRatingAlias();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function gameProduct(): BelongsTo
    {
        return $this->belongsTo(GameProduct::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderByDesc('sold_count');
    }

    public function scopeTopRated(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('rating_count', '>=', 5)
            ->orderByDesc('avg_rating');
    }

    public function scopeCheapest(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('price');
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((1 - $this->price / $this->original_price) * 100, 1);
        }
        return null;
    }

    public function recalculateRating(): void
    {
        $result = $this->reviews()->selectRaw('AVG(rating) as avg, COUNT(*) as count')->first();
        $this->update([
            'rating' => $result->avg ?? 0,
            'avg_rating' => $result->avg ?? 0,
            'rating_count' => $result->count ?? 0,
        ]);
    }

    public function getTypeAttribute(?string $value): ?ProductType
    {
        $type = self::normalizeProductType($value ?: ($this->attributes['product_type'] ?? null));

        return $type ? ProductType::tryFrom($type) : null;
    }

    public function setTypeAttribute(string|ProductType|null $value): void
    {
        $type = $value instanceof ProductType ? $value->value : self::normalizeProductType($value);

        $this->attributes['type'] = $type;

        if (empty($this->attributes['product_type'])) {
            $this->attributes['product_type'] = $type;
        }
    }

    public function setProductTypeAttribute(string|ProductType|null $value): void
    {
        $type = $value instanceof ProductType ? $value->value : self::normalizeProductType($value);

        $this->attributes['product_type'] = $type;

        if (empty($this->attributes['type'])) {
            $this->attributes['type'] = $type;
        }
    }

    public function setDeliveryTypeAttribute(string|DeliveryType|null $value): void
    {
        $deliveryType = $value instanceof DeliveryType ? $value->value : $value;

        $this->attributes['delivery_type'] = $deliveryType === 'auto'
            ? DeliveryType::INSTANT->value
            : $deliveryType;
    }

    private function syncDenormalizedCatalogFields(): void
    {
        if ($this->game_product_id) {
            $gameProduct = $this->gameProduct ?: GameProduct::find($this->game_product_id);

            if ($gameProduct) {
                $this->attributes['game_id'] ??= $gameProduct->game_id;

                $gameProductType = $gameProduct->getRawOriginal('type') ?: $gameProduct->type?->value;
                if (empty($this->attributes['type']) && $gameProductType) {
                    $this->attributes['type'] = self::normalizeProductType($gameProductType);
                }

                if (empty($this->attributes['product_type']) && $gameProductType) {
                    $this->attributes['product_type'] = self::normalizeProductType($gameProductType);
                }
            }
        }

        if (! empty($this->attributes['game_id']) && empty($this->attributes['category_id'])) {
            $game = $this->game ?: Game::find($this->attributes['game_id']);
            $this->attributes['category_id'] = $game?->category_id;
        }
    }

    private function syncRatingAlias(): void
    {
        if (array_key_exists('avg_rating', $this->attributes) && ! array_key_exists('rating', $this->attributes)) {
            $this->attributes['rating'] = $this->attributes['avg_rating'];
        }

        if (array_key_exists('rating', $this->attributes) && ! array_key_exists('avg_rating', $this->attributes)) {
            $this->attributes['avg_rating'] = $this->attributes['rating'];
        }
    }

    private static function normalizeProductType(?string $type): ?string
    {
        return match ($type) {
            'key' => ProductType::GAME_KEY->value,
            'akun' => ProductType::ACCOUNT->value,
            'koin' => ProductType::COIN->value,
            default => $type,
        };
    }
}
