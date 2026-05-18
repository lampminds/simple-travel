<?php

namespace App\Support;

use App\Models\CatHelper;
use App\Models\Language;
use App\Services\CatalogHelperQuery;
use App\Services\CatalogHelperResolver;
use Illuminate\Support\Str;

/**
 * Resolves catalog helper HTML (cat_helpers + translations) for the UI locale.
 */
final class CatalogHelperContent
{
    /**
     * HTML for the resolved helper row (locale from {@see app()->getLocale()}).
     */
    public static function htmlForQuery(CatalogHelperQuery $query): ?string
    {
        $helper = CatalogHelperResolver::resolve($query);

        return $helper === null ? null : static::htmlForHelperModel($helper);
    }

    public static function htmlForHelperModel(CatHelper $helper): ?string
    {
        $helper->loadMissing('translations');

        if ($helper->translations->isEmpty()) {
            return null;
        }

        $languageIds = $helper->translations->pluck('language_id')->unique()->filter()->all();
        if ($languageIds === []) {
            return null;
        }

        $languages = Language::query()
            ->whereIn('id', $languageIds)
            ->with('locale')
            ->get()
            ->keyBy('id');

        $appLocale = app()->getLocale();
        $resolvedLang = static::resolveLanguageForLocale($appLocale, $helper, $languages);

        if ($resolvedLang === null) {
            return null;
        }

        $row = $helper->translations->firstWhere('language_id', $resolvedLang->id);
        $html = $row?->text;

        if ($html === null || static::isEffectivelyEmptyHtml($html)) {
            return null;
        }

        return $html;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Language>  $languages
     */
    private static function resolveLanguageForLocale(
        string $appLocale,
        CatHelper $helper,
        $languages
    ): ?Language {
        $normalized = str_replace('_', '-', $appLocale);

        foreach ($helper->translations as $t) {
            $lang = $languages->get($t->language_id);
            if ($lang === null) {
                continue;
            }
            $tag = $lang->locale?->tag ?? '';
            if ($tag === '') {
                continue;
            }
            if (strcasecmp($tag, $appLocale) === 0) {
                return $lang;
            }
            if (strcasecmp(str_replace('_', '-', $tag), $normalized) === 0) {
                return $lang;
            }
        }

        $primary = Str::before($normalized, '-');

        foreach ($helper->translations as $t) {
            $lang = $languages->get($t->language_id);
            if ($lang === null) {
                continue;
            }
            $tag = $lang->locale?->tag ?? '';
            if ($tag === '') {
                continue;
            }
            if (strcasecmp(Str::before(str_replace('_', '-', $tag), '-'), $primary) === 0) {
                return $lang;
            }
        }

        $firstId = $helper->translations->sortBy('language_id')->first()?->language_id;

        return $firstId !== null ? $languages->get($firstId) : null;
    }

    public static function isEffectivelyEmptyHtml(string $html): bool
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return true;
        }
        if (preg_match('/<img[\s>]/i', $trimmed)) {
            return false;
        }

        return trim(strip_tags($trimmed)) === '';
    }
}
