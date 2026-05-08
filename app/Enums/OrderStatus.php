<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case DISPUTED = 'disputed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::PROCESSING => 'Processing',
            self::DELIVERED => 'Delivered',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::DISPUTED => 'Disputed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'info',
            self::PROCESSING => 'info',
            self::DELIVERED => 'accent',
            self::COMPLETED => 'accent',
            self::CANCELLED => 'error',
            self::REFUNDED => 'error',
            self::DISPUTED => 'warning',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, self::transitions()[$this] ?? []);
    }

    private static function transitions(): array
    {
        return [
            self::PENDING => [self::PAID, self::CANCELLED],
            self::PAID => [self::PROCESSING, self::CANCELLED, self::REFUNDED, self::DISPUTED],
            self::PROCESSING => [self::DELIVERED, self::CANCELLED, self::DISPUTED],
            self::DELIVERED => [self::COMPLETED, self::DISPUTED],
            self::COMPLETED => [self::REFUNDED, self::DISPUTED],
            self::CANCELLED => [self::REFUNDED],
            self::REFUNDED => [],
            self::DISPUTED => [self::REFUNDED, self::COMPLETED, self::CANCELLED],
        ];
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::PAID, self::PROCESSING, self::DELIVERED]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::REFUNDED]);
    }
}