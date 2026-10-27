<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ServiceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service types present in an operator's public catalog.
 * Order is the type id, with no curated sequence.
 */
final class OperatorPublicProductCategories
{
    /**
     * @return Collection<int, ServiceType>
     */
    public function forAccount(Account $account): Collection
    {
        $typeIds = $this->typeIdsFromPackages($account)
            ->merge($this->typeIdsFromDirectServices($account))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($typeIds->isEmpty()) {
            return collect();
        }

        return ServiceType::query()
            ->whereIn('id', $typeIds->all())
            ->where('active', true)
            ->whereNotIn('code', [ServiceType::CODE_PACKAGE, ServiceType::CODE_COMBO])
            ->with(['translations.language.locale'])
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, int>
     */
    private function typeIdsFromPackages(Account $account): Collection
    {
        return DB::table('operator_package_items as items')
            ->join('operator_service_catalog as catalog', 'catalog.id', '=', 'items.operator_service_catalog_id')
            ->join('service_variants as variants', 'variants.id', '=', 'items.service_variant_id')
            ->join('services', 'services.id', '=', 'variants.service_id')
            ->where('catalog.operator_id', $account->id)
            ->where('catalog.catalog_kind', 'package')
            ->where('catalog.is_public', true)
            ->where('catalog.status', 'active')
            ->pluck('services.service_type_id');
    }

    /**
     * @return Collection<int, int>
     */
    private function typeIdsFromDirectServices(Account $account): Collection
    {
        return DB::table('operator_service_catalog as catalog')
            ->join('service_offers', 'service_offers.id', '=', 'catalog.source_service_offer_id')
            ->join('service_variants as variants', 'variants.id', '=', 'service_offers.service_variant_id')
            ->join('services', 'services.id', '=', 'variants.service_id')
            ->where('catalog.operator_id', $account->id)
            ->where('catalog.catalog_kind', 'service')
            ->where('catalog.is_public', true)
            ->where('catalog.status', 'active')
            ->pluck('services.service_type_id');
    }
}
