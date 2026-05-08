<?php

namespace App\Enums;

enum UserRole: string
{
    case BUYER = 'buyer';
    case SELLER = 'seller';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::BUYER => 'Buyer',
            self::SELLER => 'Seller',
            self::ADMIN => 'Admin',
            self::SUPER_ADMIN => 'Super Admin',
        };
    }

    public function isSellerOrAbove(): bool
    {
        return match ($this) {
            self::SELLER, self::ADMIN, self::SUPER_ADMIN => true,
            default => false,
        };
    }

    public function isAdminOrAbove(): bool
    {
        return match ($this) {
            self::ADMIN, self::SUPER_ADMIN => true,
            default => false,
        };
    }
}