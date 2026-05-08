<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function search(string $query, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByGame(int $gameId, string $productType = null, int $perPage = 15): LengthAwarePaginator;

    public function findCheapest(int $gameProductId): ?Product;

    public function getPopular(int $limit = 10): Collection;

    public function findBySeller(int $sellerId, int $perPage = 15): LengthAwarePaginator;

    public function filterAndSort(array $filters, string $sort = 'popular', int $perPage = 15): LengthAwarePaginator;
}