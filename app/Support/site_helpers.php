<?php

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
