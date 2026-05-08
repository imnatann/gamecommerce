<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case FREEZE = 'freeze';
    case UNFREEZE = 'unfreeze';
    case REFUND = 'refund';
    case PAYMENT = 'payment';
    case EARNING = 'earning';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Deposit',
            self::WITHDRAWAL => 'Withdrawal',
            self::TRANSFER_IN => 'Transfer In',
            self::TRANSFER_OUT => 'Transfer Out',
            self::FREEZE => 'Freeze',
            self::UNFREEZE => 'Unfreeze',
            self::REFUND => 'Refund',
            self::PAYMENT => 'Payment',
            self::EARNING => 'Earning',
        };
    }

    public function isCredit(): bool
    {
        return in_array($this, [self::DEPOSIT, self::TRANSFER_IN, self::REFUND, self::EARNING, self::UNFREEZE]);
    }

    public function isDebit(): bool
    {
        return in_array($this, [self::WITHDRAWAL, self::TRANSFER_OUT, self::PAYMENT, self::FREEZE]);
    }
}