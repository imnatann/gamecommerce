<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\WalletTransactionType;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load(['wallet']);

        $recentOrders = Order::where('buyer_id', $user->id)
            ->with(['items.product.game'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        return view('pages.profile.index', compact('user', 'recentOrders', 'wishlistCount'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username,' . $request->user()->id,
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $user->clearMediaCollection('avatar');
            }
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function orders(Request $request): View
    {
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 15);

        $orders = Order::where('buyer_id', $request->user()->id)
            ->with(['items.product.game', 'items.product.media'])
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('pages.profile.orders', compact('orders', 'status'));
    }

    public function orderDetail(Request $request, int $orderId): View
    {
        $order = Order::with([
            'items.product.game',
            'items.product.seller',
            'items.product.media',
            'payment',
            'dispute',
        ])
            ->where('buyer_id', $request->user()->id)
            ->findOrFail($orderId);

        return view('pages.profile.order-detail', compact('order'));
    }

    public function wallet(Request $request): View
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['balance' => 0]
        );

        $transactions = $wallet->transactions()
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('pages.profile.wallet', compact('wallet', 'transactions'));
    }

    public function favorites(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 15);

        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with(['product.game', 'product.seller', 'product.media'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('pages.profile.favorites', compact('wishlists'));
    }

    public function toggleFavorite(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Produk dihapus dari favorit.');
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return back()->with('success', 'Produk ditambahkan ke favorit.');
    }
}