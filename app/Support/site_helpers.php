<?php

use App\Models\Role;
use Illuminate\Support\Facades\File;

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
