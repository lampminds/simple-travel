<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Derives tenant website account nicks from the HTTP Host header.
 *
 * Production: {nick}.{production_domain}
 * Development (when APP_URL ends with .debian): {nick}-st.debian (suffix configurable).
 */
final class TenantWebsiteHostParser
{
    /**
     * True when tenant host parsing should use the development pattern.
     * Rule: APP_URL (trimmed) ends with ".debian", or its host component ends with ".debian".
     */
    public static function isDevelopmentHostPattern(): bool
    {
        $url = rtrim((string) config('app.url'), '/');

        if (Str::endsWith($url, '.debian')) {
            return true;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && $host !== '' && Str::endsWith($host, '.debian');
    }

    /**
     * Extract account nick from request host, or null if this is not a tenant website host.
     */
    public static function extractAccountNick(string $host): ?string
    {
        $host = strtolower($host);

        if (static::isDevelopmentHostPattern()) {
            $suffix = (string) config('tenant_website.development_host_suffix', '-st.debian');
            if ($suffix === '' || ! Str::endsWith($host, $suffix)) {
                return null;
            }

            $nick = Str::beforeLast($host, $suffix);

            return $nick !== '' ? $nick : null;
        }

        $domain = strtolower((string) config('tenant_website.production_domain', 'simple-travel.net'));
        $needle = '.'.$domain;
        if (! Str::endsWith($host, $needle)) {
            return null;
        }

        $sub = substr($host, 0, -strlen($needle));
        if ($sub === '' || str_contains($sub, '.')) {
            return null;
        }

        if (in_array($sub, ['www', 'mail', 'smtp', 'ftp'], true)) {
            return null;
        }

        return $sub;
    }

    /**
     * Full URL for the tenant's public site (inverse of extractAccountNick).
     */
    public static function publicWebsiteUrlForNick(string $nick): ?string
    {
        $nick = strtolower(trim($nick));
        if ($nick === '') {
            return null;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        $scheme = parse_url($appUrl !== '' ? $appUrl : 'https://localhost', PHP_URL_SCHEME);
        $scheme = in_array($scheme, ['http', 'https'], true) ? $scheme : 'https';

        if (static::isDevelopmentHostPattern()) {
            $suffix = (string) config('tenant_website.development_host_suffix', '-st.debian');
            $host = $nick.$suffix;
        } else {
            $domain = strtolower((string) config('tenant_website.production_domain', 'simple-travel.net'));
            $host = $nick.'.'.$domain;
        }

        return $scheme.'://'.$host.'/';
    }
}
