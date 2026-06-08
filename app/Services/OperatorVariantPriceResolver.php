<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CurrencyRateSide;
use App\Models\PriceList;
use App\Models\PriceListAssignment;
use App\Models\PriceListItem;
use App\Models\ServiceVariant;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Resolves operator catalog price from variant base, assigned list line, and assignment adjustment.
 *
 * @see docs/operator-service-variant-price-calculation.md
 */
final class OperatorVariantPriceResolver
{
    public function __construct(
        private readonly PriceFormatService $priceFormatService,
        private readonly CurrencyConversionService $currencyConversion,
    ) {
    }

    /**
     * @return array{
     *     amount: float|null,
     *     has_amount: bool,
     *     currency_code: string,
     *     currency_id: int|null,
     *     formatted: string,
     *     breakdown_html: string
     * }
     */
    public function resolve(
        ServiceVariant $variant,
        int $providerAccountId,
        int $operatorAccountId,
        ?CarbonInterface $pricingDate = null,
    ): array {
        $d = Carbon::parse($pricingDate ?? Carbon::today())->startOfDay();
        $variantCurrencyCode = $this->currencyCode($variant);
        $variantCurrencyId = (int) ($variant->currency_id ?? 0);

        $lines = [];
        $baseNumeric = $variant->base_price !== null ? (float) $variant->base_price : null;

        $assignment = $this->findActiveAssignment($providerAccountId, $operatorAccountId, $d);
        if ($assignment === null) {
            if ($baseNumeric !== null) {
                $lines[] = __('account.service_offers.price_breakdown.base', [
                    'amount' => $this->formatMoney($baseNumeric, $providerAccountId),
                    'currency' => $variantCurrencyCode,
                ]);
            }

            if ($baseNumeric === null) {
                $lines[] = __('account.service_offers.price_breakdown.cannot_compute');

                return $this->pack(null, $variantCurrencyCode, $lines, $providerAccountId, $variantCurrencyId > 0 ? $variantCurrencyId : null);
            }

            return $this->pack($baseNumeric, $variantCurrencyCode, $lines, $providerAccountId, $variantCurrencyId > 0 ? $variantCurrencyId : null);
        }

        $list = $assignment->priceList;
        $code = $this->listCurrencyCode($list);
        $listCurrencyId = (int) ($list->currency_id ?? 0);

        if ($baseNumeric !== null) {
            $lines[] = __('account.service_offers.price_breakdown.base', [
                'amount' => $this->formatMoney($baseNumeric, $providerAccountId),
                'currency' => $variantCurrencyCode,
            ]);
        }

        $item = $this->findPriceListItemForVariant($list->id, $variant);

        $isFixedListPrice = $item !== null && $item->pricing_mode === 'fixed';
        $workingBase = $baseNumeric;

        if (! $isFixedListPrice && $workingBase !== null && $variantCurrencyId !== $listCurrencyId && $listCurrencyId > 0) {
            $convertedBase = $this->currencyConversion->convert(
                $workingBase,
                $variantCurrencyId,
                $listCurrencyId,
                CurrencyRateSide::Buy,
                CurrencyRateSide::Sell,
                $operatorAccountId,
                $d,
            );

            if ($convertedBase === null) {
                $lines[] = __('account.service_offers.price_breakdown.currency_conversion_failed');

                return $this->pack(null, $code, $lines, $providerAccountId, $listCurrencyId > 0 ? $listCurrencyId : null);
            }

            $lines[] = __('account.service_offers.price_breakdown.fx_conversion', [
                'from' => $variantCurrencyCode,
                'to' => $code,
                'amount' => $this->formatMoney($convertedBase, $operatorAccountId),
                'currency' => $code,
            ]);

            $workingBase = $convertedBase;
        }

        $afterLine = $this->computeAfterListLine($workingBase, $item);

        if ($item !== null) {
            if ($afterLine === null) {
                $lines[] = __('account.service_offers.price_breakdown.needs_base_for_compose');

                return $this->pack(null, $code, $lines, $providerAccountId, $listCurrencyId > 0 ? $listCurrencyId : null);
            }

            $listName = (string) $list->name;
            if ($item->pricing_mode === null || $item->pricing_mode === '') {
                $lines[] = __('account.service_offers.price_breakdown.list_variant_base', [
                    'name' => $listName,
                    'amount' => $this->formatMoney((float) $afterLine, $operatorAccountId),
                    'currency' => $code,
                ]);
            } elseif ($item->pricing_mode === 'fixed') {
                $lines[] = __('account.service_offers.price_breakdown.list_final', [
                    'name' => $listName,
                    'amount' => $this->formatMoney($afterLine, $operatorAccountId),
                    'currency' => $code,
                ]);
            } elseif ($item->pricing_mode === 'percentage') {
                $effect = $this->signedPercentLabel((float) $item->price);
                $lines[] = __('account.service_offers.price_breakdown.list_with_effect', [
                    'name' => $listName,
                    'effect' => $effect,
                    'amount' => $this->formatMoney($afterLine, $operatorAccountId),
                    'currency' => $code,
                ]);
            } else {
                $lines[] = __('account.service_offers.price_breakdown.cannot_compute');

                return $this->pack(null, $code, $lines, $providerAccountId, $listCurrencyId > 0 ? $listCurrencyId : null);
            }
        }

        if ($afterLine === null) {
            $lines[] = __('account.service_offers.price_breakdown.cannot_compute');

            return $this->pack(null, $code, $lines, $providerAccountId, $listCurrencyId > 0 ? $listCurrencyId : null);
        }

        $final = $this->applyAssignmentAdjustment($afterLine, $assignment);
        $adjType = (string) ($assignment->adjustment_type ?? 'none');
        $adjVal = (float) ($assignment->adjustment_value ?? 0.0);

        if ($adjType === 'percentage' && abs($adjVal) > 1.0e-9) {
            $effect = $this->signedPercentLabel($adjVal);
            $lines[] = __('account.service_offers.price_breakdown.adjustment', [
                'effect' => $effect,
                'amount' => $this->formatMoney($final, $operatorAccountId),
                'currency' => $code,
            ]);
        } elseif ($adjType === 'fixed' && abs($adjVal) > 1.0e-9) {
            $effect = $this->signedNumberLabel($adjVal);
            $lines[] = __('account.service_offers.price_breakdown.adjustment', [
                'effect' => $effect,
                'amount' => $this->formatMoney($final, $operatorAccountId),
                'currency' => $code,
            ]);
        }

        return $this->pack($final, $code, $lines, $operatorAccountId, $listCurrencyId > 0 ? $listCurrencyId : null);
    }

    /**
     * @param  array{amount?: float|null, has_amount?: bool}  $resolved
     */
    public function resolvedAmountIsZero(array $resolved): bool
    {
        if (! ($resolved['has_amount'] ?? false)) {
            return false;
        }

        return abs((float) ($resolved['amount'] ?? 0.0)) < 1.0e-9;
    }

    /**
     * @param  list<string>  $lines
     * @return array{amount: float|null, has_amount: bool, currency_code: string, currency_id: int|null, formatted: string, breakdown_html: string}
     */
    private function pack(?float $amount, string $currencyCode, array $lines, ?int $accountId = null, ?int $currencyId = null): array
    {
        $has = $amount !== null;
        $html = '<div class="price-breakdown-popover text-start small lh-sm" style="max-width: 22rem;">';
        foreach ($lines as $line) {
            $html .= '<div>'.e($line).'</div>';
        }
        $html .= '</div>';

        return [
            'amount' => $amount,
            'has_amount' => $has,
            'currency_code' => $currencyCode,
            'currency_id' => $currencyId,
            'formatted' => $has
                ? $this->priceFormatService->formatWithCurrency((float) $amount, currencyCode: $currencyCode, accountId: $accountId)
                : '—',
            'breakdown_html' => $html,
        ];
    }

    private function currencyCode(ServiceVariant $variant): string
    {
        if ($variant->relationLoaded('currency') && $variant->currency) {
            if (! $variant->currency->relationLoaded('lmpCurrency')) {
                $variant->currency->loadMissing('lmpCurrency');
            }

            return $variant->currency->currency_code;
        }

        return '—';
    }

    /**
     * ISO code for amounts resolved through an assigned provider price list.
     */
    private function listCurrencyCode(PriceList $list): string
    {
        $list->loadMissing('currency.lmpCurrency');

        if ($list->currency) {
            return $list->currency->currency_code;
        }

        return '—';
    }

    private function formatMoney(float $n, ?int $accountId = null): string
    {
        return $this->priceFormatService->format($n, $accountId);
    }

    private function signedPercentLabel(float $p): string
    {
        if (abs($p - round($p)) < 0.0001) {
            return sprintf('%+.0f%%', $p);
        }

        return sprintf('%+.1f%%', $p);
    }

    private function signedNumberLabel(float $v): string
    {
        if (abs($v - round($v)) < 0.0001) {
            return sprintf('%+.0f', $v);
        }

        return sprintf('%+.2f', $v);
    }

    private function computeAfterListLine(?float $base, ?PriceListItem $item): ?float
    {
        if ($item === null) {
            return $base;
        }

        $L = (float) $item->price;

        if ($item->pricing_mode === 'fixed') {
            return $L;
        }

        if ($item->pricing_mode === 'percentage') {
            if ($base === null) {
                return null;
            }

            return $base + ($base * ($L / 100.0));
        }

        if ($item->pricing_mode === null || $item->pricing_mode === '') {
            return $base;
        }

        return null;
    }

    private function findPriceListItemForVariant(int $priceListId, ServiceVariant $variant): ?PriceListItem
    {
        return PriceListItem::query()
            ->where('provider_price_list_id', $priceListId)
            ->where('service_variant_id', $variant->id)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Active provider→operator price list assignment for today (if any).
     */
    public function activeAssignment(
        int $providerAccountId,
        int $operatorAccountId,
        ?\Carbon\CarbonInterface $pricingDate = null,
    ): ?PriceListAssignment {
        return $this->findActiveAssignment(
            $providerAccountId,
            $operatorAccountId,
            Carbon::parse($pricingDate ?? Carbon::today())->startOfDay(),
        );
    }

    private function applyAssignmentAdjustment(float $amount, PriceListAssignment $assignment): float
    {
        return match ($assignment->adjustment_type) {
            'percentage' => $amount * (1.0 + ((float) ($assignment->adjustment_value ?? 0.0) / 100.0)),
            'fixed' => $amount + (float) ($assignment->adjustment_value ?? 0.0),
            default => $amount,
        };
    }

    private function findActiveAssignment(int $providerAccountId, int $operatorAccountId, Carbon $d): ?PriceListAssignment
    {
        $assignments = PriceListAssignment::query()
            ->where('operator_id', $operatorAccountId)
            ->where('is_active', true)
            ->whereHas('priceList', function ($q) use ($providerAccountId, $d): void {
                $q->where('provider_id', $providerAccountId)
                    ->where('is_active', true)
                    ->where(function ($q2) use ($d): void {
                        $q2->whereNull('valid_from')
                            ->orWhereDate('valid_from', '<=', $d);
                    })
                    ->where(function ($q2) use ($d): void {
                        $q2->whereNull('valid_to')
                            ->orWhereDate('valid_to', '>=', $d);
                    });
            })
            ->with(['priceList.currency.lmpCurrency'])
            ->orderByDesc('id')
            ->get();

        foreach ($assignments as $assignment) {
            if (! $this->assignmentDatesMatch($assignment, $d)) {
                continue;
            }

            return $assignment;
        }

        return null;
    }

    private function assignmentDatesMatch(PriceListAssignment $assignment, Carbon $d): bool
    {
        return $this->windowContains($assignment->valid_from, $assignment->valid_to, $d);
    }

    /**
     * @param  \DateTimeInterface|string|null  $from
     * @param  \DateTimeInterface|string|null  $to
     */
    private function windowContains($from, $to, Carbon $d): bool
    {
        if ($from) {
            $fromDay = Carbon::parse($from)->toDateString();
            if ($d->toDateString() < $fromDay) {
                return false;
            }
        }
        if ($to) {
            $toDay = Carbon::parse($to)->toDateString();
            if ($d->toDateString() > $toDay) {
                return false;
            }
        }

        return true;
    }
}
