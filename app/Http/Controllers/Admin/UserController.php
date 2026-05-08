<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['wallet', 'roles']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        $users = $query->latest()->paginate(20);

        $totalUsers = User::count();
        $totalSellers = User::role('seller')->count();
        $totalAdmins = User::role('admin')->count();

        return view('admin.users', compact('users', 'totalUsers', 'totalSellers', 'totalAdmins'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:buyer,seller,admin',
        ]);

        $user->assignPlatformRole($request->role);

        return back()->with('success', 'Role pengguna berhasil diperbarui');
    }

    public function verifyKYC(User $user)
    {
        $user->update(['kyc_status' => 'verified']);

        return back()->with('success', 'KYC berhasil diverifikasi');
    }

    public function ban(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user->update([
            'meta' => array_merge($user->meta ?? [], [
                'banned' => true,
                'ban_reason' => $request->reason,
                'banned_at' => now()->toDateTimeString(),
            ]),
        ]);

        return back()->with('success', 'Pengguna berhasil dibanned');
    }

    public function unban(User $user)
    {
        $user->update([
            'meta' => array_merge($user->meta ?? [], [
                'banned' => false,
                'ban_reason' => null,
                'banned_at' => null,
            ]),
        ]);

        return back()->with('success', 'Ban pengguna berhasil dicabut');
    }
}
