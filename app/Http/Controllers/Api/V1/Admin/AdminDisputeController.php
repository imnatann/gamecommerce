<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Enums\DisputeStatus;
use App\Models\Dispute;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDisputeController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $status = $request->query('status');

        $disputes = Dispute::with(['order', 'buyer', 'seller', 'messages'])
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginateResponse($disputes);
    }

    public function show(int $id): JsonResponse
    {
        $dispute = Dispute::with(['order.items.product', 'buyer', 'seller', 'messages.user', 'messages.media'])
            ->findOrFail($id);

        return $this->successResponse($dispute);
    }

    public function resolve(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'resolution' => 'required|string|max:2000',
            'refund_buyer' => 'required|boolean',
            'release_seller' => 'required|boolean',
        ]);

        $dispute = Dispute::findOrFail($id);

        if ($dispute->status !== DisputeStatus::OPEN->value) {
            return $this->errorResponse('Dispute tidak dapat diselesaikan karena status bukan open.', 400);
        }

        try {
            DB::beginTransaction();

            $dispute->update([
                'status' => DisputeStatus::RESOLVED->value,
                'resolution' => $validated['resolution'],
                'resolved_at' => now(),
                'resolved_by' => $request->user()->id,
            ]);

            $order = $dispute->order;

            if ($validated['refund_buyer']) {
                $order->update(['status' => 'refunded']);
                $buyerWallet = $order->buyer->wallet;
                if ($buyerWallet) {
                    $buyerWallet->increment('balance', $order->final_amount);
                    $buyerWallet->transactions()->create([
                        'type' => 'refund',
                        'amount' => $order->final_amount,
                        'balance_before' => $buyerWallet->balance - $order->final_amount,
                        'balance_after' => $buyerWallet->balance,
                        'description' => "Refund untuk order #{$order->id}",
                    ]);
                }
            }

            if ($validated['release_seller'] && !$validated['refund_buyer']) {
                $order->update(['status' => 'completed']);
                $sellerWallet = $order->items->first()->product->seller->wallet;
                if ($sellerWallet) {
                    $sellerEarnings = $order->final_amount * (1 - config('gamecommerce.platform_fee', 5) / 100);
                    $sellerWallet->increment('balance', $sellerEarnings);
                }
            }

            DB::commit();

            return $this->successResponse($dispute->fresh()->load(['order', 'buyer', 'seller', 'messages']), 'Dispute berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Gagal menyelesaikan dispute.', 500);
        }
    }

    public function reply(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:2048',
        ]);

        $dispute = Dispute::findOrFail($id);

        $message = $dispute->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_admin' => true,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $message->addMedia($file)->toMediaCollection('dispute_attachments');
            }
        }

        return $this->successResponse($message->load(['user', 'media']), 'Balasan berhasil dikirim.');
    }
}