<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        if ($user && (! $user->last_activity_at || $user->last_activity_at->diffInMinutes(now()) >= 5)) {
            $timestamps = $user->timestamps;
            $user->timestamps = false;
            $user->forceFill(['last_activity_at' => now()])->saveQuietly();
            $user->timestamps = $timestamps;
        }

        return $response;
    }
}
