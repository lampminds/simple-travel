<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

/**
 * Sets the application locale from a Language record (id from app's languages table).
 * Used by the website language switcher; stores locale in session and redirects back.
 */
class SetLocaleController extends Controller
{
    public function __invoke(int $language): RedirectResponse
    {
        $lang = Language::with('lmpLanguage')->findOrFail($language);
        $lmp = $lang->lmpLanguage;
        $code = $lmp->code ?? $lmp->code2 ?? null;

        if (is_string($code) && $code !== '') {
            session(['locale' => $code]);
            app()->setLocale($code);
        }

        return redirect()->back();
    }
}
