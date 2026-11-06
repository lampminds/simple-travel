<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageItem;
use App\Models\Service;
use App\Models\ServiceVariant;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Builds labels and picker rows for operator package lines (price lists, etc.).
 */
final class OperatorPackageItemSelectOptions
{
    /**
     * @return array<int, string> package_item_id => label
     */
    public function optionsForOperator(int $operatorAccountId): array
    {
        $options = [];
        foreach ($this->itemsForOperator($operatorAccountId) as $item) {
            $options[(int) $item->id] = $this->label($item);
        }

        return $options;
    }

    /**
     * Structured rows for the price-list line picker.
     * Names stay separate from schedule, day, provider and inclusion so similar lines can be told apart.
     *
     * @return list<array{
     *     id: int,
     *     kind: string,
     *     package_name: string,
     *     sku: string,
     *     service_name: string,
     *     variant_name: string,
     *     schedule: string,
     *     day_number: int|null,
     *     day_label: string,
     *     inclusion: string,
     *     quantity: int,
     *     provider: string,
     *     search: string
     * }>
     */
    public function catalogForOperator(int $operatorAccountId): array
    {
        $catalog = [];
        foreach ($this->itemsForOperator($operatorAccountId) as $item) {
            $catalog[] = $this->catalogRecord($item);
        }

        return $catalog;
    }

    /**
     * @return Collection<int, OperatorPackageItem>
     */
    public function itemsForOperator(int $operatorAccountId): Collection
    {
        return OperatorPackageItem::query()
            ->whereHas('catalog', function ($query) use ($operatorAccountId): void {
                $query->where('operator_id', $operatorAccountId);
            })
            ->with([
                'catalog.translations',
                'service',
                'serviceVariant.currency.lmpCurrency',
                'serviceVariant.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
                'serviceOffer.providerAccount',
            ])
            ->orderBy('operator_service_catalog_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function label(OperatorPackageItem $item): string
    {
        $record = $this->catalogRecord($item);

        $segments = [$record['kind'], $record['package_name']];

        $serviceSegment = trim(implode(' — ', array_filter([
            $record['sku'],
            $record['service_name'],
            $record['variant_name'],
        ], static fn (string $part): bool => $part !== '')));

        if ($serviceSegment !== '') {
            $segments[] = $serviceSegment;
        }

        if ($record['day_label'] !== '') {
            $segments[] = $record['day_label'];
        }

        if ($record['inclusion'] !== '') {
            $segments[] = $record['inclusion'];
        }

        if ($record['provider'] !== '') {
            $segments[] = $record['provider'];
        }

        return implode(' › ', $segments);
    }

    /**
     * @return array{
     *     id: int,
     *     kind: string,
     *     package_name: string,
     *     sku: string,
     *     service_name: string,
     *     variant_name: string,
     *     schedule: string,
     *     day_number: int|null,
     *     day_label: string,
     *     inclusion: string,
     *     quantity: int,
     *     provider: string,
     *     search: string
     * }
     */
    private function catalogRecord(OperatorPackageItem $item): array
    {
        $packageName = $item->catalog?->displayLabel() ?? '#'.$item->operator_service_catalog_id;
        $kind = $item->catalog?->isDirectService()
            ? __('account.catalog_kind.service')
            : __('account.catalog_kind.package');

        $variant = $item->serviceVariant;
        $sku = '';
        $variantName = '';
        $schedule = '';
        $serviceName = '';

        if ($variant instanceof ServiceVariant) {
            $sku = trim((string) ($variant->sku ?? ''));
            $variantName = trim((string) $variant->name);
            $serviceName = trim((string) ($variant->service?->name ?? ''));
            $schedule = $this->scheduleLabel($variant);
        }

        if ($serviceName === '' && $item->service instanceof Service) {
            $serviceName = trim((string) $item->service->name);
        }

        if ($serviceName === '') {
            $serviceName = __('account.operator_price_lists.package_item_unknown_service');
        }

        if ($variantName !== '' && (strcasecmp($variantName, $serviceName) === 0 || strcasecmp($variantName, $sku) === 0)) {
            $variantName = '';
        }

        $dayNumber = $item->day_number !== null ? (int) $item->day_number : null;
        $dayLabel = $dayNumber !== null
            ? __('account.operator_price_lists.package_item_day', ['day' => $dayNumber])
            : '';

        $inclusionMode = (string) $item->inclusion_mode;
        $inclusion = in_array($inclusionMode, ['included', 'optional', 'upgrade'], true)
            ? __('account.operator_packages.inclusion_mode.'.$inclusionMode)
            : $inclusionMode;

        $quantity = max(1, (int) ($item->quantity ?? 1));

        $provider = '';
        if ($item->serviceOffer?->providerAccount !== null) {
            $providerAccount = $item->serviceOffer->providerAccount;
            $provider = trim((string) ($providerAccount->commercial_name ?? $providerAccount->name ?? $providerAccount->nick ?? ''));
        }

        $search = mb_strtolower(implode(' ', array_filter([
            $kind,
            $packageName,
            $sku,
            $serviceName,
            $variantName,
            $schedule,
            $dayLabel,
            $dayNumber !== null ? (string) $dayNumber : '',
            $inclusion,
            $provider,
        ], static fn (string $part): bool => $part !== '')), 'UTF-8');

        return [
            'id' => (int) $item->id,
            'kind' => $kind,
            'package_name' => $packageName,
            'sku' => $sku,
            'service_name' => $serviceName,
            'variant_name' => $variantName,
            'schedule' => $schedule,
            'day_number' => $dayNumber,
            'day_label' => $dayLabel,
            'inclusion' => $inclusion,
            'quantity' => $quantity,
            'provider' => $provider,
            'search' => $search,
        ];
    }

    private function scheduleLabel(ServiceVariant $variant): string
    {
        $start = $this->clockLabel($variant->start_time);
        $end = $this->clockLabel($variant->end_time);

        if ($start !== '' && $end !== '') {
            return $start.'–'.$end;
        }

        return $start !== '' ? $start : $end;
    }

    private function clockLabel(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('H:i');
        }

        $string = trim((string) $value);
        if (preg_match('/^(\d{2}:\d{2})/', $string, $matches) === 1) {
            return $matches[1];
        }

        return '';
    }
}
