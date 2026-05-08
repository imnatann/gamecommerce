<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends ApiBaseController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $search = $request->query('search');
        $role = $request->query('role');
        $status = $request->query('status');

        $users = User::with(['wallet', 'roles'])
            ->when($search, fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
            ))
            ->when($role, fn ($q, $r) => $q->role($r))
            ->when($status === 'banned', fn ($q) => $q->where('is_banned', true))
            ->when($status === 'verified', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->when($status === 'kyc_verified', fn ($q) => $q->where('kyc_status', 'verified'))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->paginateResponse($users);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with(['wallet', 'roles', 'ordersAsBuyer', 'products', 'reviews', 'disputesAsBuyer', 'disputesAsSeller'])
            ->findOrFail($id);

        $stats = [
            'total_orders' => $user->ordersAsBuyer()->count(),
            'total_spent' => $user->ordersAsBuyer()->where('status', 'completed')->sum('total_amount'),
            'total_products' => $user->products()->count(),
            'total_sales' => $user->hasRole('seller')
                ? Order::whereHas('items.product', fn ($q) => $q->where('seller_id', $user->id))
                    ->where('status', 'completed')->sum('total_amount')
                : 0,
            'avg_rating' => $user->products()->avg('avg_rating'),
        ];

        return $this->successResponse(['user' => $user, 'stats' => $stats]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:buyer,seller,admin',
            'is_banned' => 'nullable|boolean',
            'kyc_status' => 'nullable|in:pending,verified,rejected',
        ]);

        $role = $validated['role'] ?? null;
        unset($validated['role']);

        $user->update($validated);

        if ($role) {
            $user->assignPlatformRole($role);
        }

        return $this->successResponse($user->fresh()->load(['wallet', 'roles']), 'User berhasil diperbarui.');
    }

    public function ban(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return $this->errorResponse('Admin tidak dapat dibanned.', 403);
        }

        $user->update(['is_banned' => true]);
        $user->tokens()->delete();

        return $this->successResponse($user->fresh(), 'User berhasil dibanned.');
    }

    public function unban(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => false]);

        return $this->successResponse($user->fresh(), 'Ban user berhasil dicabut.');
    }

    public function verifyKyc(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['kyc_status' => 'verified']);

        return $this->successResponse($user->fresh(), 'KYC user berhasil diverifikasi.');
    }

    public function rejectKyc(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'kyc_status' => 'rejected',
            'kyc_rejection_reason' => $validated['reason'],
        ]);

        return $this->successResponse($user->fresh(), 'KYC user ditolak.');
    }

    public function resetPassword(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $newPassword = str()->random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        return $this->successResponse(['new_password' => $newPassword], 'Password user berhasil direset.');
    }
}
