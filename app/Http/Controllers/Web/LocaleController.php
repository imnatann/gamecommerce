<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Supported locales — harus sesuai dengan SetLocale middleware.
     */
    private const SUPPORTED = ['id', 'en'];

    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, self::SUPPORTED, true)) {
            abort(404);
        }

        session()->put('locale', $locale);

        return redirect()->back()->with('success', 'Bahasa berhasil diubah.');
    }
}
