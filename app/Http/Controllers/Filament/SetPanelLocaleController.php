<?php

namespace App\Http\Controllers\Filament;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

/**
 * Sets the application locale from a Language record (id from app's languages table).
 * Uses the primary language subtag from cat_locales (e.g. en from en-US) for Laravel's locale.
 */
class SetPanelLocaleController extends Controller
{
    public function __invoke(int $language): RedirectResponse
    {
        $lang = Language::with('locale')->findOrFail($language);
        $code = $lang->locale?->primaryLanguageTag();

        if (is_string($code) && $code !== '') {
            session(['locale' => $code]);
            app()->setLocale($code);
        }

        return redirect()->back();
    }
}
