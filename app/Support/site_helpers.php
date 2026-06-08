<?php

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

if (! function_exists('locale_for_carbon')) {
    /**
     * Map the Laravel app locale to a Carbon-compatible locale tag.
     */
    function locale_for_carbon(?string $appLocale = null): string
    {
        $appLocale = $appLocale ?? app()->getLocale();

        return match ($appLocale) {
            'pt' => 'pt_BR',
            default => $appLocale,
        };
    }
}

if (! function_exists('locale_date')) {
    /**
     * Format a date for display in the current app locale (e.g. 1-jun-2026 in Spanish).
     */
    function locale_date(mixed $date, ?string $appLocale = null): string
    {
        $carbon = Carbon::parse($date)->locale(locale_for_carbon($appLocale));

        $day = $carbon->format('j');
        $month = str_replace('.', '', strtolower($carbon->translatedFormat('M')));
        $year = $carbon->format('Y');

        return $day.' '.$month.' '.$year;
    }
}

if (! function_exists('locale_date_range')) {
    /**
     * Format an optional from/to date range for display in the current app locale.
     */
    function locale_date_range(mixed $from, mixed $to, ?string $appLocale = null, ?string $openLabel = null): string
    {
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;

        if ($fromDate && $toDate) {
            return locale_date($fromDate, $appLocale).' -> '.locale_date($toDate, $appLocale);
        }

        if ($fromDate) {
            return __('account.service_offers.operator_preview_valid_from', [
                'date' => locale_date($fromDate, $appLocale),
            ]);
        }

        if ($toDate) {
            return __('account.service_offers.operator_preview_valid_to', [
                'date' => locale_date($toDate, $appLocale),
            ]);
        }

        return $openLabel ?? __('account.service_offers.operator_preview_valid_open');
    }
}

if (! function_exists('locale_date_input_placeholder')) {
    function locale_date_input_placeholder(?string $appLocale = null): string
    {
        return (string) __('forms.date_input_placeholder');
    }
}

if (! function_exists('locale_date_input_php_format')) {
    /**
     * PHP date format for parsing user-typed dates (es/pt: day first).
     */
    function locale_date_input_php_format(?string $appLocale = null): string
    {
        $appLocale = $appLocale ?? app()->getLocale();
        $primary = strtolower(explode('-', str_replace('_', '-', $appLocale))[0]);

        return match ($primary) {
            'en' => 'm/d/Y',
            default => 'd/m/Y',
        };
    }
}

if (! function_exists('locale_date_input_js_format')) {
    /**
     * Hint for the locale-date-input script (display + parse pattern).
     *
     * @return array{pattern: string, placeholder: string, regex: string}
     */
    function locale_date_input_js_format(?string $appLocale = null): array
    {
        $appLocale = $appLocale ?? app()->getLocale();
        $primary = strtolower(explode('-', str_replace('_', '-', $appLocale))[0]);

        if ($primary === 'en') {
            return [
                'pattern' => 'mdy',
                'placeholder' => 'mm/dd/yyyy',
                'regex' => '^\\d{1,2}/\\d{1,2}/\\d{4}$',
            ];
        }

        return [
            'pattern' => 'dmy',
            'placeholder' => locale_date_input_placeholder($appLocale),
            'regex' => '^\\d{1,2}/\\d{1,2}/\\d{4}$',
        ];
    }
}

if (! function_exists('format_date_for_input')) {
    /**
     * Format a stored date for locale-aware text inputs (e.g. 01/04/2026 in Spanish).
     */
    function format_date_for_input(mixed $date, ?string $appLocale = null): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        try {
            $carbon = Carbon::parse($date);
        } catch (\Throwable) {
            return '';
        }

        return $carbon->format(locale_date_input_php_format($appLocale));
    }
}

if (! function_exists('parse_date_input')) {
    /**
     * Parse a locale date field (d/m/Y or Y-m-d) into Y-m-d for persistence.
     */
    function parse_date_input(?string $value, ?string $appLocale = null): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }

        $format = locale_date_input_php_format($appLocale);

        try {
            $parsed = Carbon::createFromFormat($format, $value);

            return $parsed->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}

if (! function_exists('normalize_form_date_value')) {
    /**
     * Normalize a model/old value to Y-m-d for date form inputs.
     */
    function normalize_form_date_value(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_string($value)) {
            return parse_date_input($value) ?? '';
        }

        return '';
    }
}

if (! function_exists('normalize_request_locale_dates')) {
    /**
     * @param  list<string>  $keys
     */
    function normalize_request_locale_dates(\Illuminate\Http\Request $request, array $keys): void
    {
        foreach ($keys as $key) {
            $value = $request->input($key);
            if ($value === null || $value === '') {
                continue;
            }

            $parsed = parse_date_input(is_string($value) ? $value : (string) $value);
            if ($parsed !== null) {
                $request->merge([$key => $parsed]);
            }
        }
    }
}

if (! function_exists('locale_datetime')) {
    /**
     * Format a date and time for display in the current app locale (e.g. 1-jun-2026 14:30).
     */
    function locale_datetime(mixed $date, ?string $appLocale = null): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        $carbon = Carbon::parse($date)->locale(locale_for_carbon($appLocale));

        return locale_date($carbon, $appLocale).' '.$carbon->format('H:i');
    }
}

if (! function_exists('locale_currency_code')) {
    /**
     * Default display currency for the current app locale.
     */
    function locale_currency_code(?string $appLocale = null): string
    {
        $primary = strtolower(explode('-', str_replace('_', '-', $appLocale ?? app()->getLocale()))[0]);

        return match ($primary) {
            'es' => 'ARS',
            'pt' => 'BRL',
            default => 'USD',
        };
    }
}

if (! function_exists('site_asset')) {
    /**
     * URL for a file under public/{tenant site assets_path}/ (e.g. images/bg/1.jpg).
     */
    function site_asset(string $path): string
    {
        $base = trim((string) config('tenant_site.assets_path', 'site/assets'), '/');
        $path = ltrim($path, '/');

        return asset($base.'/'.$path);
    }
}

if (! function_exists('site_assets_published')) {
    /**
     * True when the published assets directory exists (has at least tailwind.css).
     */
    function site_assets_published(): bool
    {
        $base = trim((string) config('tenant_site.assets_path', 'site/assets'), '/');

        return File::exists(public_path($base.'/css/tailwind.css'));
    }
}

if (! function_exists('getAccountRoles')) {
    /**
     * Return assignable roles for a specific account.
     *
     * @param  int  $accountId
     * @param  array<int, string>  $excludedNames
     * @return array<int, string>
     */
    function getAccountRoles(int $accountId, array $excludedNames = []): array
    {
        if ($accountId < 1) {
            return [];
        }

        $query = Role::query()
            ->where('account_id', $accountId)
            ->orderBy('name');

        if ($excludedNames !== []) {
            $query->whereNotIn('name', $excludedNames);
        }

        return $query->pluck('name', 'id')->all();
    }
}

if (! function_exists('is_dev_server')) {
    /**
     * True when the app is served from a dev host (APP_URL contains "debian", case-insensitive).
     */
    function is_dev_server(): bool
    {
        $url = (string) config('app.url', '');

        return $url !== '' && str_contains(strtolower($url), 'debian');
    }
}
