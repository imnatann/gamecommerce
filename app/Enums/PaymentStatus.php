<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case EXPIRED = 'expired';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::EXPIRED => 'Expired',
            self::REFUNDED => 'Refunded',
        };
    }

    public function isSuccessful(): bool
    {
        return $this === self::SUCCESS;
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::SUCCESS, self::FAILED, self::EXPIRED, self::REFUNDED]);
    }
}