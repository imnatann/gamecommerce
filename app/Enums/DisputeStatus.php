<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case OPEN = 'open';
    case UNDER_REVIEW = 'under_review';
    case RESOLVED_BUYER = 'resolved_buyer';
    case RESOLVED_SELLER = 'resolved_seller';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::UNDER_REVIEW => 'Under Review',
            self::RESOLVED_BUYER => 'Resolved (Buyer)',
            self::RESOLVED_SELLER => 'Resolved (Seller)',
            self::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'warning',
            self::UNDER_REVIEW => 'info',
            self::RESOLVED_BUYER => 'accent',
            self::RESOLVED_SELLER => 'accent',
            self::CLOSED => 'muted',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::OPEN, self::UNDER_REVIEW]);
    }

    public function isResolved(): bool
    {
        return in_array($this, [self::RESOLVED_BUYER, self::RESOLVED_SELLER, self::CLOSED]);
    }
}