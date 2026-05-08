<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GameProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'type',
        'name',
        'slug',
        'required_info',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'required_info' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function boot(): void
    {
        parent::boot();
        static::creating(function (self $gameProduct) {
            if (empty($gameProduct->slug)) {
                $gameProduct->slug = Str::slug($gameProduct->name);
            }
        });
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeOfType($query, ProductType $type)
    {
        return $query->where('type', $type);
    }
}