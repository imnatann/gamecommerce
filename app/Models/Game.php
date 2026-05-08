<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Game extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'banner',
        'is_active',
        'category_id',
        'category',
        'region',
        'sort_order',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'meta' => 'array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
    }

    public static function boot(): void
    {
        parent::boot();
        static::creating(function (self $game) {
            if (empty($game->slug)) {
                $game->slug = Str::slug($game->name);
            }
        });
        static::updating(function (self $game) {
            if ($game->isDirty('name') && !$game->isDirty('slug')) {
                $game->slug = Str::slug($game->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function gameProducts(): HasMany
    {
        return $this->hasMany(GameProduct::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopePopular($query)
    {
        return $query->where('is_active', true)->orderByDesc('sort_order');
    }
}
