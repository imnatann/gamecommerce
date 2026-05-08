<?php

namespace App\Enums;

enum ProductType: string
{
    case TOPUP = 'topup';
    case GAME_KEY = 'game_key';
    case ITEM = 'item';
    case ACCOUNT = 'account';
    case VOUCHER = 'voucher';
    case JOKI = 'joki';
    case COIN = 'coin';

    public function label(): string
    {
        return match ($this) {
            self::TOPUP => 'Top Up',
            self::GAME_KEY => 'Game Key',
            self::ITEM => 'Item',
            self::ACCOUNT => 'Akun',
            self::VOUCHER => 'Voucher',
            self::JOKI => 'Joki',
            self::COIN => 'Koin Game',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TOPUP => 'battery-charging',
            self::GAME_KEY => 'key',
            self::ITEM => 'package',
            self::ACCOUNT => 'user',
            self::VOUCHER => 'ticket',
            self::JOKI => 'swords',
            self::COIN => 'coins',
        };
    }

    public function deliveryIsInstant(): bool
    {
        return in_array($this, [self::TOPUP, self::GAME_KEY, self::VOUCHER, self::COIN]);
    }

    public function requiresServerInfo(): bool
    {
        return in_array($this, [self::TOPUP, self::ITEM, self::JOKI]);
    }
}