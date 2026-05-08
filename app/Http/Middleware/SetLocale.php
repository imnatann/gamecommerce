<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supportedLocales = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->user()?->locale
            ?? $request->getPreferredLanguage($this->supportedLocales)
            ?? config('app.locale', 'id');

        if (! in_array($locale, $this->supportedLocales)) {
            $locale = 'id';
        }

        app()->setLocale($locale);
        session()->put('locale', $locale);

        return $next($request);
    }
}