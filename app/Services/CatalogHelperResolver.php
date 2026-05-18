<?php

namespace App\Services;

use App\Models\CatHelper;
use Illuminate\Support\Collection;

/**
 * Resolves the best matching cat_helpers row for a screen + helper key and optional scopes.
 *
 * Rows with null service_type_id / account_type_id apply to all types; a row scoped to the
 * current service type or account type wins over the generic row when both exist.
 */
final class CatalogHelperResolver
{
    public static function resolve(CatalogHelperQuery $query): ?CatHelper
    {
        $builder = CatHelper::query()
            ->where('screen_code', $query->screenCode)
            ->where('code', $query->code)
            ->where('active', true)
            ->with(['translations']);

        if ($query->serviceTypeId !== null) {
            $builder->where(function ($q) use ($query): void {
                $q->whereNull('service_type_id')
                    ->orWhere('service_type_id', $query->serviceTypeId);
            });
        } else {
            $builder->whereNull('service_type_id');
        }

        if ($query->accountTypeId !== null) {
            $builder->where(function ($q) use ($query): void {
                $q->whereNull('account_type_id')
                    ->orWhere('account_type_id', $query->accountTypeId);
            });
        } else {
            $builder->whereNull('account_type_id');
        }

        /** @var Collection<int, CatHelper> $candidates */
        $candidates = $builder->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        return $candidates
            ->sort(function (CatHelper $a, CatHelper $b) use ($query): int {
                $ra = static::scopeRank($a, $query);
                $rb = static::scopeRank($b, $query);
                if ($ra !== $rb) {
                    return $rb <=> $ra;
                }

                return $a->id <=> $b->id;
            })
            ->first();
    }

    /**
     * Higher rank = more specific match (preferred).
     */
    private static function scopeRank(CatHelper $row, CatalogHelperQuery $query): int
    {
        $rank = 0;
        if ($query->serviceTypeId !== null && $row->service_type_id === $query->serviceTypeId) {
            $rank += 2;
        }
        if ($query->accountTypeId !== null && $row->account_type_id === $query->accountTypeId) {
            $rank += 1;
        }

        return $rank;
    }
}
