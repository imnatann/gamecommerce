<?php

namespace App\Repositories\Contracts;

use App\Models\Game;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GameRepositoryInterface
{
    public function all(): Collection;

    public function findBySlug(string $slug): ?Game;

    public function popular(int $limit = 10): Collection;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    public function withProducts(int $gameId): ?Game;
}