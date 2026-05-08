<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $seller = Auth::user();
        return view('seller.settings', compact('seller'));
    }

    public function updateProfile(Request $request)
    {
        $seller = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'response_time' => 'nullable|string|max:50',
            'operational_hours' => 'nullable|array',
        ]);

        if ($request->hasFile('avatar')) {
            if ($seller->avatar) {
                Storage::disk('public')->delete($seller->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $seller->update($validated);

        return back()->with('success', 'Profil toko berhasil diperbarui');
    }

    public function updateNotifications(Request $request)
    {
        $seller = Auth::user();

        $notifications = $request->input('notifications', []);

        $seller->update([
            'meta' => array_merge($seller->meta ?? [], [
                'notifications' => $notifications,
            ]),
        ]);

        return back()->with('success', 'Pengaturan notifikasi berhasil diperbarui');
    }
}