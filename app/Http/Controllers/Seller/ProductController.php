<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();

        $query = Product::where('seller_id', $seller->id)
            ->with('gameProduct.game');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(20);

        return view('seller.products', compact('products'));
    }

    public function create()
    {
        $games = Game::active()->orderBy('sort_order')->get();
        return view('seller.product-form', compact('games'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_product_id' => 'required|exists:game_products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:100',
            'original_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'server' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'delivery_type' => 'required|in:instant,manual',
            'delivery_data' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['seller_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $games = Game::active()->orderBy('sort_order')->get();
        $gameProducts = GameProduct::where('game_id', $product->gameProduct->game_id)->active()->get();

        return view('seller.product-form', compact('product', 'games', 'gameProducts'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'game_product_id' => 'required|exists:game_products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:100',
            'original_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'server' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'delivery_type' => 'required|in:instant,manual',
            'delivery_data' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function toggleActive(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success', $product->is_active ? 'Produk diaktifkan' : 'Produk dinonaktifkan');
    }
}