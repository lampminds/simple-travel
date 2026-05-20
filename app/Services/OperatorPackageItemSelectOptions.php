<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageItem;
use App\Models\Service;

/**
 * Builds select labels for operator package lines (price lists, etc.).
 */
final class OperatorPackageItemSelectOptions
{
    /**
     * @return array<int, string> package_item_id => label
     */
    public function optionsForOperator(int $operatorAccountId): array
    {
        $items = OperatorPackageItem::query()
            ->whereHas('catalog', function ($query) use ($operatorAccountId): void {
                $query->where('operator_id', $operatorAccountId);
            })
            ->with([
                'catalog.translations',
                'service',
                'serviceVariant',
                'serviceOffer.providerAccount',
            ])
            ->orderBy('operator_service_catalog_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $options = [];
        foreach ($items as $item) {
            $options[(int) $item->id] = $this->label($item);
        }

        return $options;
    }

    public function label(OperatorPackageItem $item): string
    {
        $packageName = $item->catalog?->displayLabel() ?? '#'.$item->operator_service_catalog_id;
        $serviceName = $item->service instanceof Service
            ? (string) $item->service->name
            : __('account.operator_price_lists.package_item_unknown_service');

        $variantPart = '';
        if ($item->serviceVariant !== null) {
            $sku = trim((string) ($item->serviceVariant->sku ?? ''));
            $variantPart = $sku !== '' ? ' ('.$sku.')' : '';
        }

        $segments = [$packageName, $serviceName.$variantPart];

        if ($item->day_number !== null) {
            $segments[] = __('account.operator_price_lists.package_item_day', ['day' => $item->day_number]);
        }

        $segments[] = __('account.operator_price_lists.inclusion_mode.'.$item->inclusion_mode);

        if ($item->serviceOffer?->providerAccount !== null) {
            $provider = $item->serviceOffer->providerAccount;
            $providerLabel = (string) ($provider->commercial_name ?? $provider->name ?? $provider->nick ?? '');
            if ($providerLabel !== '') {
                $segments[] = $providerLabel;
            }
        }

        return implode(' › ', $segments);
    }
}
