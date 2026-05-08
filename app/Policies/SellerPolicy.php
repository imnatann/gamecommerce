<?php

namespace App\Policies;

use App\Models\User;

class SellerPolicy
{
    public function manage(User $user): bool
    {
        return $user->isSeller();
    }

    public function accessPanel(User $user): bool
    {
        return $user->isSeller();
    }

    public function submitKyc(User $user): bool
    {
        return $user->isSeller()
            && ! $user->isKycVerified()
            && $user->getRawOriginal('kyc_status') !== 'pending';
    }
}
