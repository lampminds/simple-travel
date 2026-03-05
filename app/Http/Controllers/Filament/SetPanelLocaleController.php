<?php

namespace App\Http\Controllers\Filament;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

/**
 * Sets the application locale from a Language record (id from app's languages table).
 * Reads the locale code from addons.lmp_languages and stores it in session.
 */
class SetPanelLocaleController extends Controller
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
