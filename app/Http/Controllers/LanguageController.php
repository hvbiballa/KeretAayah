<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(string $locale, Request $request): RedirectResponse
    {
        $supportedLocales = config('app.supported_locales', ['ms', 'en']);

        $request->session()->put(
            'locale',
            in_array($locale, $supportedLocales, true) ? $locale : config('app.locale', 'ms'),
        );

        return redirect()->back();
    }
}
