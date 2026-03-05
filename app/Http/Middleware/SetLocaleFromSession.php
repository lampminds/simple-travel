<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application locale from session when the user has chosen a language
 * (e.g. via the Filament language switcher).
 */
class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
            if (is_string($locale) && $locale !== '') {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
