<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->unauthenticated($request);
        }

        if (! $user->isKycVerified()) {
            $message = match ($user->kyc_status) {
                'pending' => 'Verifikasi KYC Anda sedang diproses. Harap tunggu.',
                'rejected' => 'Verifikasi KYC Anda ditolak. Silakan ajukan kembali.',
                default => 'Silakan lengkapi verifikasi KYC terlebih dahulu.',
            };

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $message], 403);
            }

            $route = Route::has('seller.kyc.verify')
                ? route('seller.kyc.verify')
                : (Route::has('seller.settings') ? route('seller.settings') : route('home'));

            return redirect($route)->with('warning', $message);
        }

        return $next($request);
    }

    private function unauthenticated(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(Route::has('login') ? route('login') : url('/auth/login'));
    }
}
