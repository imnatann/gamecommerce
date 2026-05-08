<?php

namespace App\Enums;

enum DeliveryType: string
{
    case INSTANT = 'instant';
    case MANUAL = 'manual';
    case LOGIN = 'login';

    public function label(): string
    {
        return match ($this) {
            self::INSTANT => 'Instant',
            self::MANUAL => 'Manual',
            self::LOGIN => 'Login',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::INSTANT => 'Delivered automatically within seconds',
            self::MANUAL => 'Seller sends manually within delivery time',
            self::LOGIN => 'Account credentials provided after purchase',
        };
    }

    public function estimatedDelivery(): string
    {
        return match ($this) {
            self::INSTANT => '0-5 minutes',
            self::MANUAL => '5-30 minutes',
            self::LOGIN => 'Instant after verification',
        };
    }
}