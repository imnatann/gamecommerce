<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBanned()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Akun Anda telah dinonaktifkan. Hubungi support@gamecommerce.id jika ini kesalahan.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()->route('login')
                ->with('error', $message);
        }

        return $next($request);
    }
}
