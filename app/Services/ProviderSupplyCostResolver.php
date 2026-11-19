<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\PriceListItem;
use App\Models\ProviderSupplyPriceListAssignment;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Internal cost a buyer provider sees for a supplier variant.
 * This amount is not the price offered to operators.
 */
final class ProviderSupplyCostResolver
{
    public function __construct(
        private readonly PriceFormatService $priceFormat,
    ) {
    }

    /**
     * @param  Collection<int, ServiceVariant>  $variants
     * @return array<int, string> variant id => formatted cost
     */
    public function labelsForVariants(int $buyerAccountId, Collection $variants): array
    {
        $labels = [];

        $bySupplier = $variants->groupBy(fn (ServiceVariant $variant): int => (int) ($variant->service?->account_id ?? 0));

        foreach ($bySupplier as $supplierAccountId => $supplierVariants) {
            $supplierAccountId = (int) $supplierAccountId;
            if ($supplierAccountId < 1 || $supplierAccountId === $buyerAccountId) {
                continue;
            }

            $assignment = $this->activeAssignment($supplierAccountId, $buyerAccountId);
            $items = $assignment === null
                ? collect()
                : PriceListItem::query()
                    ->where('provider_price_list_id', $assignment->provider_price_list_id)
                    ->whereIn('service_variant_id', $supplierVariants->map(fn (ServiceVariant $variant): int => (int) $variant->id)->all())
                    ->get()
                    ->keyBy(fn (PriceListItem $item): int => (int) $item->service_variant_id);

            foreach ($supplierVariants as $variant) {
                $label = $this->labelFor($variant, $assignment, $items->get((int) $variant->id), $buyerAccountId);
                if ($label !== null) {
                    $labels[(int) $variant->id] = $label;
                }
            }
        }

        return $labels;
    }

    /**
     * Price of every variant the account can put in a combo: the assigned
     * supplier cost for another provider's variant, or its own base price.
     *
     * @param  Collection<int, mixed>  $variants
     * @return array<int, string> variant id => formatted price
     */
    public function componentPriceLabels(int $accountId, Collection $variants): array
    {
        $variants = $variants
            ->filter(fn (mixed $variant): bool => $variant instanceof ServiceVariant)
            ->values();

        $labels = $this->labelsForVariants($accountId, $variants);

        foreach ($variants as $variant) {
            $variantId = (int) $variant->id;
            if (isset($labels[$variantId])) {
                continue;
            }

            $ownerId = (int) ($variant->service?->account_id ?? 0);
            if ($ownerId !== $accountId) {
                continue;
            }

            $label = $this->basePriceLabel($variant, $accountId);
            if ($label !== null) {
                $labels[$variantId] = $label;
            }
        }

        return $labels;
    }

    private function basePriceLabel(ServiceVariant $variant, int $accountId): ?string
    {
        if ($variant->base_price === null || $variant->base_price === '') {
            return null;
        }

        $currencyCode = $this->currencyCode($variant->currency);

        return $this->priceFormat->formatWithCurrency(
            $variant->base_price,
            null,
            $currencyCode !== '' ? $currencyCode : null,
            $accountId,
        );
    }

    private function activeAssignment(int $supplierAccountId, int $buyerAccountId): ?ProviderSupplyPriceListAssignment
    {
        $today = Carbon::today();

        $assignments = ProviderSupplyPriceListAssignment::query()
            ->where('buyer_account_id', $buyerAccountId)
            ->where('is_active', true)
            ->whereHas('priceList', function ($query) use ($supplierAccountId): void {
                $query->where('provider_id', $supplierAccountId)
                    ->where('is_active', true);
            })
            ->with('priceList.currency.lmpCurrency')
            ->orderByDesc('id')
            ->get();

        foreach ($assignments as $assignment) {
            if ($this->windowContains($assignment->valid_from, $assignment->valid_to, $today)) {
                return $assignment;
            }
        }

        return null;
    }

    private function labelFor(
        ServiceVariant $variant,
        ?ProviderSupplyPriceListAssignment $assignment,
        ?PriceListItem $item,
        int $buyerAccountId,
    ): ?string {
        $base = $variant->base_price !== null ? (float) $variant->base_price : null;
        $amount = $base;
        $currencyCode = $this->currencyCode($variant->currency);

        if ($item !== null && $item->price !== null) {
            $line = (float) $item->price;
            if ($item->pricing_mode === 'fixed') {
                $amount = $line;
                $currencyCode = $this->currencyCode($assignment?->priceList?->currency) ?: $currencyCode;
            } elseif ($item->pricing_mode === 'percentage' && $base !== null) {
                $amount = $base + ($base * ($line / 100.0));
            }
        }

        if ($amount === null) {
            return null;
        }

        if ($assignment !== null) {
            $amount = $this->applyAdjustment($amount, $assignment);
        }

        return $this->priceFormat->formatWithCurrency($amount, null, $currencyCode !== '' ? $currencyCode : null, $buyerAccountId);
    }

    private function applyAdjustment(float $amount, ProviderSupplyPriceListAssignment $assignment): float
    {
        return match ($assignment->adjustment_type) {
            'percentage' => $amount * (1.0 + ((float) ($assignment->adjustment_value ?? 0.0) / 100.0)),
            'fixed' => $amount + (float) ($assignment->adjustment_value ?? 0.0),
            default => $amount,
        };
    }

    private function currencyCode(?Currency $currency): string
    {
        return strtoupper(trim((string) ($currency?->lmpCurrency?->code ?? '')));
    }

    private function windowContains(mixed $from, mixed $to, Carbon $date): bool
    {
        if ($from) {
            if ($date->toDateString() < Carbon::parse($from)->toDateString()) {
                return false;
            }
        }

        if ($to) {
            if ($date->toDateString() > Carbon::parse($to)->toDateString()) {
                return false;
            }
        }

        return true;
    }
}
