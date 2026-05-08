<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSeller() && $user->isKycVerified();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isSeller()
            && $user->isKycVerified()
            && $user->id === $product->seller_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isSeller()
            && $user->id === $product->seller_id;
    }

    public function toggleActive(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }
}
