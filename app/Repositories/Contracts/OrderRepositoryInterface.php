<?php

namespace App\Repositories\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function findByBuyer(int $buyerId, OrderStatus $status = null, int $perPage = 15): LengthAwarePaginator;

    public function findBySeller(int $sellerId, OrderStatus $status = null, int $perPage = 15): LengthAwarePaginator;

    public function findByStatus(OrderStatus $status, int $perPage = 15): LengthAwarePaginator;

    public function getStats(int $userId = null): array;
}