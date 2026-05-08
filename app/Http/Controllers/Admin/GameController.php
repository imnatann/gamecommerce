<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::withCount('gameProducts');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $games = $query->orderBy('sort_order')->paginate(20);

        return view('admin.games', compact('games'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'region' => 'nullable|string|max:10',
            'icon' => 'nullable|image|max:512',
            'banner' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('games/icons', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('games/banners', 'public');
        }

        Game::create($validated);

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil ditambahkan');
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'region' => 'nullable|string|max:10',
            'icon' => 'nullable|image|max:512',
            'banner' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('icon')) {
            if ($game->icon) {
                Storage::disk('public')->delete($game->icon);
            }
            $validated['icon'] = $request->file('icon')->store('games/icons', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($game->banner) {
                Storage::disk('public')->delete($game->banner);
            }
            $validated['banner'] = $request->file('banner')->store('games/banners', 'public');
        }

        $game->update($validated);

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil diperbarui');
    }

    public function toggleActive(Game $game)
    {
        $game->update(['is_active' => !$game->is_active]);

        return back()->with('success', $game->is_active ? 'Game diaktifkan' : 'Game dinonaktifkan');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('success', 'Game berhasil dihapus');
    }
}