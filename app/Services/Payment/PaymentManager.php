<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentManager
{
    public function __construct(
        private MidtransService $midtransService,
        private XenditService $xenditService,
    ) {}

    public function gateway(?string $gateway = null): PaymentServiceInterface
    {
        return match (strtolower($gateway ?: $this->defaultGateway())) {
            'midtrans' => $this->midtransService,
            'xendit' => $this->xenditService,
            default => throw new InvalidArgumentException('Unsupported payment gateway.'),
        };
    }

    public function defaultGateway(): string
    {
        $gateway = strtolower((string) config('gamecommerce.payment.default_gateway', 'midtrans'));

        return in_array($gateway, ['midtrans', 'xendit'], true) ? $gateway : 'midtrans';
    }

    public function availableGateways(): array
    {
        return ['midtrans', 'xendit'];
    }

    public function getAvailableMethods(): array
    {
        return [
            'ewallets' => [
                ['id' => 'gopay', 'name' => 'GoPay'],
                ['id' => 'ovo', 'name' => 'OVO'],
                ['id' => 'dana', 'name' => 'DANA'],
                ['id' => 'shopeepay', 'name' => 'ShopeePay'],
                ['id' => 'linkaja', 'name' => 'LinkAja'],
            ],
            'banks' => [
                ['id' => 'bca', 'name' => 'BCA'],
                ['id' => 'bni', 'name' => 'BNI'],
                ['id' => 'bri', 'name' => 'BRI'],
                ['id' => 'mandiri', 'name' => 'Mandiri'],
                ['id' => 'permata', 'name' => 'Permata'],
                ['id' => 'cimb', 'name' => 'CIMB Niaga'],
            ],
            'convenience' => [
                ['id' => 'alfamart', 'name' => 'Alfamart'],
                ['id' => 'indomaret', 'name' => 'Indomaret'],
            ],
            'other' => [
                ['id' => 'qris', 'name' => 'QRIS'],
                ['id' => 'credit_card', 'name' => 'Credit Card'],
                ['id' => 'debit_card', 'name' => 'Debit Card'],
                ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
                ['id' => 'e_wallet', 'name' => 'E-Wallet'],
            ],
        ];
    }
}
