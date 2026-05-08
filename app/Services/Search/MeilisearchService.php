<?php

namespace App\Services\Search;

use App\Models\Game;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class MeilisearchService
{
    private array $indexableModels = [
        Game::class => [
            'index' => 'games',
            'searchable' => ['name', 'slug', 'description', 'category_name'],
            'filterable' => ['category_id', 'is_active', 'sort_order'],
            'sortable' => ['sort_order', 'created_at', 'products_count'],
        ],
        Product::class => [
            'index' => 'products',
            'searchable' => ['name', 'description', 'server', 'game_name'],
            'filterable' => ['game_id', 'type', 'is_active', 'seller_id', 'price', 'rating', 'sold_count', 'server'],
            'sortable' => ['price', 'rating', 'sold_count', 'created_at'],
        ],
    ];

    public function indexModel(object $model): bool
    {
        try {
            $config = $this->indexableModels[get_class($model)] ?? null;

            if (!$config) {
                Log::warning('Meilisearch: model not indexable', ['model' => get_class($model)]);

                return false;
            }

            if (method_exists($model, 'shouldSearchable') && !$model->shouldSearchable()) {
                return true;
            }

            $searchableData = $this->getSearchableData($model, $config);
            $model->searchable();

            Log::info('Meilisearch: model indexed', [
                'model' => get_class($model),
                'id' => $model->id,
                'index' => $config['index'],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Meilisearch: failed to index model', [
                'model' => get_class($model),
                'id' => $model->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function removeFromIndex(object $model): bool
    {
        try {
            $model->unsearchable();

            Log::info('Meilisearch: model removed from index', [
                'model' => get_class($model),
                'id' => $model->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Meilisearch: failed to remove model from index', [
                'model' => get_class($model),
                'id' => $model->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function search(string $query, ?string $index = null, array $filters = [], string $sort = null, int $limit = 20): array
    {
        try {
            $results = [];

            $indices = $index ? [$index] : ['games', 'products'];

            foreach ($indices as $idx) {
                $modelClass = $this->getModelClassForIndex($idx);

                if (!$modelClass) {
                    continue;
                }

                $searchBuilder = $modelClass::search($query);

                if (!empty($filters)) {
                    $filterStrings = $this->buildFilterStrings($filters);
                    if (!empty($filterStrings)) {
                        $searchBuilder = $searchBuilder->query(fn ($q) => $q->where($filters));
                    }
                }

                $searchResults = $searchBuilder->take($limit)->get();

                $results[$idx] = [
                    'hits' => $searchResults->toArray(),
                    'total' => $searchResults->count(),
                ];
            }

            Log::info('Meilisearch: search performed', [
                'query' => $query,
                'indices' => $indices,
                'filters' => $filters,
            ]);

            return [
                'success' => true,
                'query' => $query,
                'results' => $results,
            ];
        } catch (\Exception $e) {
            Log::error('Meilisearch: search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'query' => $query,
                'results' => [],
                'error' => 'Search failed: ' . $e->getMessage(),
            ];
        }
    }

    public function searchGames(string $query, int $limit = 10): array
    {
        return $this->search($query, 'games', [], null, $limit);
    }

    public function searchProducts(string $query, array $filters = [], string $sort = 'popular', int $limit = 20): array
    {
        return $this->search($query, 'products', $filters, $sort, $limit);
    }

    public function rebuildIndex(?string $index = null): array
    {
        try {
            $indices = $index ? [$index] : ['games', 'products'];
            $results = [];

            foreach ($indices as $idx) {
                $modelClass = $this->getModelClassForIndex($idx);

                if (!$modelClass) {
                    continue;
                }

                $modelClass::makeAllSearchable();

                $results[$idx] = 'rebuilt';
            }

            Log::info('Meilisearch: index rebuilt', ['indices' => $indices]);

            return [
                'success' => true,
                'rebuilt' => $results,
            ];
        } catch (\Exception $e) {
            Log::error('Meilisearch: failed to rebuild index', [
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to rebuild index: ' . $e->getMessage(),
            ];
        }
    }

    private function getSearchableData(object $model, array $config): array
    {
        $data = [];

        foreach ($config['searchable'] as $field) {
            if (str_contains($field, '_')) {
                $data[$field] = $this->resolveRelationField($model, $field);
            } else {
                $data[$field] = $model->{$field} ?? null;
            }
        }

        $data['id'] = $model->id;
        $data['created_at'] = $model->created_at?->timestamp;

        return $data;
    }

    private function resolveRelationField(object $model, string $field): mixed
    {
        return match ($field) {
            'category_name' => $model->category?->name ?? null,
            'game_name' => $model->game?->name ?? null,
            default => $model->{$field} ?? null,
        };
    }

    private function buildFilterStrings(array $filters): array
    {
        return collect($filters)->map(fn ($value, $key) => "{$key} = \"{$value}\"")->values()->toArray();
    }

    private function getModelClassForIndex(string $index): ?string
    {
        return match ($index) {
            'games' => Game::class,
            'products' => Product::class,
            default => null,
        };
    }
}