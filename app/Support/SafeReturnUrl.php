<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Keeps a same-host return path for the public website login button.
 */
final class SafeReturnUrl
{
    public static function remember(Request $request): void
    {
        $target = self::toAbsolute($request->query('return'), $request);
        if ($target !== null) {
            $request->session()->put('url.intended', $target);
        }
    }

    /**
     * Relative path stored as the post-login destination, or the fallback.
     */
    public static function relativePath(Request $request, string $fallback): string
    {
        $relative = self::toRelative($request->session()->get('url.intended'), $request);

        return $relative ?? $fallback;
    }

    public static function toAbsolute(mixed $return, Request $request): ?string
    {
        if (! is_string($return)) {
            return null;
        }

        $return = trim($return);
        if ($return === '' || str_starts_with($return, '//') || str_starts_with($return, '/\\')) {
            return null;
        }

        $parts = parse_url($return);
        if ($parts === false) {
            return null;
        }

        if (isset($parts['host']) && strcasecmp((string) $parts['host'], $request->getHost()) !== 0) {
            return null;
        }

        if (isset($parts['scheme']) && ! in_array(strtolower((string) $parts['scheme']), ['http', 'https'], true)) {
            return null;
        }

        $path = $parts['path'] ?? '/';
        if ($path === '' || ! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $request->getSchemeAndHttpHost().$path.$query.$fragment;
    }

    public static function toRelative(mixed $absolute, Request $request): ?string
    {
        $checked = self::toAbsolute($absolute, $request);
        if ($checked === null) {
            return null;
        }

        $parts = parse_url($checked);
        if ($parts === false) {
            return null;
        }

        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $path.$query.$fragment;
    }
}
