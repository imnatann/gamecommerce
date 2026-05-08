<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends ApiBaseController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
        ]);

        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($existingReview) {
            return $this->errorResponse('Anda sudah memberikan review untuk produk ini.', 400);
        }

        $hasPurchased = \App\Models\OrderItem::whereHas('order', fn ($q) => $q
            ->where('buyer_id', $request->user()->id)
            ->where('status', 'completed')
        )->where('product_id', $validated['product_id'])->exists();

        if (!$hasPurchased) {
            return $this->errorResponse('Anda harus membeli produk ini terlebih dahulu.', 403);
        }

        try {
            DB::beginTransaction();

            $review = Review::create([
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'is_anonymous' => $validated['is_anonymous'] ?? false,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $review->addMedia($image)->toMediaCollection('review_images');
                }
            }

            $product = \App\Models\Product::find($validated['product_id']);
            $newAvgRating = $product->reviews()->avg('rating');
            $product->update(['avg_rating' => round($newAvgRating, 2)]);

            DB::commit();

            return $this->successResponse($review->load(['user', 'media']), 'Review berhasil ditambahkan.', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Gagal menambahkan review.', 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $review = Review::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $review->update($validated);

        $product = $review->product;
        $newAvgRating = $product->reviews()->avg('rating');
        $product->update(['avg_rating' => round($newAvgRating, 2)]);

        return $this->successResponse($review->fresh()->load(['user', 'media']), 'Review berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $product = $review->product;
        $review->delete();

        $newAvgRating = $product->reviews()->avg('rating');
        $product->update(['avg_rating' => $newAvgRating ? round($newAvgRating, 2) : 0]);

        return $this->successResponse(null, 'Review berhasil dihapus.');
    }
}