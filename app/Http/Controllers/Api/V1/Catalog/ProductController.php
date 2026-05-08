<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Product;
use App\Models\Review;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends ApiBaseController
{
    public function __construct(
        private ProductRepositoryInterface $productRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'game_id', 'category_id', 'product_type',
            'min_price', 'max_price', 'server', 'region',
            'delivery_type', 'seller_id',
        ]);

        $sort = $request->query('sort', 'popular');
        $perPage = (int) $request->query('per_page', 20);

        $products = $this->productRepo->search('', $filters, $sort, $perPage);

        return $this->paginateResponse($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = Cache::remember("api.products.{$id}", 600, fn () => Product::with([
            'game', 'seller', 'category', 'media',
        ])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail());

        $reviews = Review::with(['user', 'media'])
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $relatedProducts = Cache::remember("api.products.related.{$id}", 900, fn () => Product::with(['game', 'seller', 'media'])
            ->where('game_id', $product->game_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->orderByDesc('sold_count')
            ->limit(6)
            ->get());

        return $this->successResponse([
            'product' => $product,
            'reviews' => $reviews,
            'related_products' => $relatedProducts,
        ]);
    }

    public function reviews(int $id, Request $request): JsonResponse
    {
        $product = Product::where('id', $id)->where('is_active', true)->firstOrFail();
        $perPage = (int) $request->query('per_page', 15);
        $sort = $request->query('sort', 'newest');

        $reviews = Review::with(['user', 'media'])
            ->where('product_id', $product->id)
            ->when($sort === 'highest', fn ($q) => $q->orderByDesc('rating'))
            ->when($sort === 'lowest', fn ($q) => $q->orderBy('rating'))
            ->when($sort === 'newest', fn ($q) => $q->orderByDesc('created_at'))
            ->when($sort === 'helpful', fn ($q) => $q->orderByDesc('helpful_count'))
            ->paginate($perPage);

        return $this->paginateResponse($reviews);
    }

    public function cheapest(int $gameProductId): JsonResponse
    {
        $product = $this->productRepo->findCheapest($gameProductId);

        if (!$product) {
            return $this->notFoundResponse('Produk termurah tidak ditemukan.');
        }

        return $this->successResponse($product->load(['seller', 'media']));
    }
}