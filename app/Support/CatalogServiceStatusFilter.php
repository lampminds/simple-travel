<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Catalog list filter for {@see Service::status}.
 *
 * Default ("all") excludes terminated services; any specific status shows only that status.
 */
final class CatalogServiceStatusFilter
{
    public const TERMINATED = 'terminated';

    /** Query param value for the default filter (all except terminated). */
    public const QUERY_ALL = 'all';

    /**
     * Statuses available as explicit filter options (including terminated).
     *
     * @return list<string>
     */
    public static function selectableStatuses(): array
    {
        return [
            'active',
            'onhold',
            'suspended',
            'discontinued',
            'inactive',
            self::TERMINATED,
        ];
    }

    /**
     * Resolve filter from request. null = default (all except terminated).
     */
    public static function resolveFromRequest(Request $request): ?string
    {
        $raw = trim((string) $request->query('status', ''));

        if ($raw === '' || strcasecmp($raw, self::QUERY_ALL) === 0) {
            return null;
        }

        return in_array($raw, self::selectableStatuses(), true) ? $raw : null;
    }

    /**
     * @param  Builder<\App\Models\Service>  $query
     */
    public static function applyToQuery(Builder $query, ?string $statusFilter): void
    {
        if ($statusFilter === null) {
            $query->where('status', '!=', self::TERMINATED);

            return;
        }

        $query->where('status', $statusFilter);
    }

    public static function labelFor(?string $statusFilter): string
    {
        if ($statusFilter === null) {
            return __('catalog.filter_status_default');
        }

        $key = 'filament.resources.service_status.'.$statusFilter;
        $label = __($key);

        return $label !== $key ? $label : $statusFilter;
    }
}
