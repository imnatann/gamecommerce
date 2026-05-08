<?php

namespace App\Models;

use App\Enums\DisputeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'buyer_id',
        'seller_id',
        'reason',
        'description',
        'evidence',
        'status',
        'resolution',
        'resolution_deadline',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DisputeStatus::class,
            'evidence' => 'array',
            'resolution_deadline' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', DisputeStatus::OPEN);
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', DisputeStatus::UNDER_REVIEW);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
