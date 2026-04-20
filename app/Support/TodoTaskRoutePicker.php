<?php

namespace App\Support;

use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;

/**
 * Builds a searchable list of named GET routes for onboarding task "route" actions.
 */
final class TodoTaskRoutePicker
{
    /**
     * @return list<string>
     */
    private static function excludedNamePrefixes(): array
    {
        return [
            'filament.',
            'livewire.',
            'ignition.',
            'horizon.',
            'pulse.',
            'telescope.',
            'nova.',
            'debugbar.',
        ];
    }

    /**
     * @return array<string, string> route name => label
     */
    public static function search(string $search, int $limit = 75): array
    {
        $needle = strtolower(trim($search));

        $matches = [];

        foreach (Route::getRoutes() as $route) {
            if (! $route instanceof LaravelRoute) {
                continue;
            }

            $name = $route->getName();
            if ($name === null || $name === '') {
                continue;
            }

            foreach (self::excludedNamePrefixes() as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    continue 2;
                }
            }

            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            if ($needle !== '' && ! str_contains(strtolower($name), $needle)) {
                continue;
            }

            $matches[$name] = self::formatLabel($route, $name);
        }

        ksort($matches, SORT_NATURAL | SORT_FLAG_CASE);

        if (count($matches) <= $limit) {
            return $matches;
        }

        return array_slice($matches, 0, $limit, true);
    }

    public static function labelForName(string $name): ?string
    {
        $route = Route::getRoutes()->getByName($name);

        return $route instanceof LaravelRoute ? self::formatLabel($route, $name) : null;
    }

    private static function formatLabel(LaravelRoute $route, string $name): string
    {
        $uri = $route->uri();

        return $name.' — /'.$uri;
    }
}
