<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateReviewAction
{
    public function execute(int $userId, int $orderId, array $validatedData): Review
    {
        return DB::transaction(function () use ($userId, $orderId, $validatedData) {
            $order = \App\Models\Order::where('id', $orderId)
                ->where('buyer_id', $userId)
                ->first();

            if (!$order) {
                throw new \RuntimeException('Order not found or does not belong to you');
            }

            if (!in_array($order->status->value, [
                OrderStatus::COMPLETED->value,
                OrderStatus::DELIVERED->value,
            ])) {
                throw new \RuntimeException('You can only review orders that have been delivered or completed');
            }

            $productId = $validatedData['product_id'];

            $orderItem = $order->items()->where('product_id', $productId)->first();

            if (!$orderItem) {
                throw new \RuntimeException('This product was not part of your order');
            }

            $existingReview = Review::where('order_id', $orderId)
                ->where('product_id', $productId)
                ->where('user_id', $userId)
                ->first();

            if ($existingReview) {
                throw new \RuntimeException('You have already reviewed this product for this order');
            }

            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'seller_id' => $orderItem->product->seller_id,
                'rating' => $validatedData['rating'],
                'title' => $validatedData['title'] ?? null,
                'content' => $validatedData['content'] ?? null,
                'is_anonymous' => $validatedData['is_anonymous'] ?? false,
            ]);

            $this->updateProductRating($productId);

            Log::info('Review created', [
                'review_id' => $review->id,
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'rating' => $validatedData['rating'],
            ]);

            return $review;
        });
    }

    private function updateProductRating(int $productId): void
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        $ratingStats = Review::where('product_id', $productId)
            ->selectRaw('
                COALESCE(AVG(rating), 0) as avg_rating,
                COUNT(*) as total_reviews,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        $product->update([
            'rating' => round($ratingStats->avg_rating, 2),
            'review_count' => $ratingStats->total_reviews,
        ]);

        $sellerId = $product->seller_id;

        if ($sellerId) {
            $sellerRating = Product::where('seller_id', $sellerId)
                ->selectRaw('COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as total_reviews')
                ->first();

            \App\Models\User::where('id', $sellerId)->update([
                'seller_rating' => round($sellerRating->avg_rating, 2),
                'seller_review_count' => $sellerRating->total_reviews,
            ]);
        }
    }
}