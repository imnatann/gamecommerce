<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->unauthenticated($request);
        }

        if (! $user->isSeller()) {
            return $this->forbidden($request, 'Anda tidak memiliki akses penjual.');
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

    private function forbidden(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => $message], 403);
        }

        abort(403, $message);
    }
}
