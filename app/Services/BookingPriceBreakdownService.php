<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Support\BookingPassengersSnapshot;

/**
 * Builds a human-readable price breakdown from a stored booking and its line items.
 */
final class BookingPriceBreakdownService
{
    public function __construct(private readonly PriceFormatService $priceFormatService)
    {
    }

    /**
     * @return array{
     *     has_lines: bool,
     *     currency_code: string|null,
     *     lines: list<array{
     *         label: string,
     *         provider_name: string|null,
     *         pricing_type: string,
     *         pricing_type_label: string,
     *         unit_price: float,
     *         unit_price_formatted: string,
     *         quantity: int,
     *         quantity_explanation: string,
     *         line_total: float,
     *         line_total_formatted: string
     *     }>,
     *     lines_subtotal: float,
     *     lines_subtotal_formatted: string,
     *     adjustment: array{
     *         type: string|null,
     *         value: float|null,
     *         amount: float,
     *         label: string,
     *         amount_formatted: string
     *     }|null,
     *     grand_total: float,
     *     grand_total_formatted: string
     * }
     */
    public function build(Booking $booking): array
    {
        $booking->loadMissing(['items', 'currency']);

        $accountId = (int) $booking->operator_id;
        $currencyCode = $booking->currency?->currency_code;
        $passengersSnapshot = is_array($booking->passengers_snapshot)
            ? $booking->passengers_snapshot
            : null;

        $lines = [];
        $linesSubtotal = 0.0;

        /** @var BookingItem $item */
        foreach ($booking->items->sortBy('id') as $item) {
            $snapshot = is_array($item->package_snapshot) ? $item->package_snapshot : [];
            $unitPrice = (float) $item->price;
            $lineTotal = (float) $item->total;
            $linesSubtotal += $lineTotal;

            $pricingType = trim((string) ($snapshot['pricing_type'] ?? ''));

            $lines[] = [
                'label' => $this->lineLabel($snapshot),
                'provider_name' => $this->providerName($snapshot),
                'pricing_type' => $pricingType,
                'pricing_type_label' => $this->pricingTypeLabel($pricingType),
                'unit_price' => $unitPrice,
                'unit_price_formatted' => $this->priceFormatService->formatWithCurrency(
                    $unitPrice,
                    currencyCode: $currencyCode,
                    accountId: $accountId,
                ),
                'quantity' => (int) $item->quantity,
                'quantity_explanation' => $this->quantityExplanation(
                    (int) $item->quantity,
                    $snapshot,
                    $passengersSnapshot,
                ),
                'line_total' => $lineTotal,
                'line_total_formatted' => $this->priceFormatService->formatWithCurrency(
                    $lineTotal,
                    currencyCode: $currencyCode,
                    accountId: $accountId,
                ),
            ];
        }

        $grandTotal = (float) $booking->subtotal;
        $adjustment = $this->resolveAdjustment($booking, $linesSubtotal, $grandTotal, $currencyCode, $accountId);

        return [
            'has_lines' => $lines !== [],
            'currency_code' => $currencyCode,
            'lines' => $lines,
            'lines_subtotal' => round($linesSubtotal, 2),
            'lines_subtotal_formatted' => $this->priceFormatService->formatWithCurrency(
                $linesSubtotal,
                currencyCode: $currencyCode,
                accountId: $accountId,
            ),
            'adjustment' => $adjustment,
            'grand_total' => $grandTotal,
            'grand_total_formatted' => $this->priceFormatService->formatWithCurrency(
                $grandTotal,
                currencyCode: $currencyCode,
                accountId: $accountId,
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function lineLabel(array $snapshot): string
    {
        $serviceName = trim((string) ($snapshot['service_name'] ?? ''));
        if ($serviceName !== '') {
            return $serviceName;
        }

        $sku = trim((string) ($snapshot['variant_sku'] ?? ''));

        return $sku !== '' ? $sku : '—';
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function providerName(array $snapshot): ?string
    {
        $name = trim((string) ($snapshot['provider_name'] ?? ''));

        return $name !== '' ? $name : null;
    }

    private function pricingTypeLabel(string $pricingType): string
    {
        if ($pricingType === '') {
            return '—';
        }

        $key = 'filament.resources.service_variant_pricing_type.'.$pricingType;
        $label = __($key);

        return $label !== $key ? $label : $pricingType;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @param  array<string, int>|null  $passengersSnapshot
     */
    private function quantityExplanation(int $billableQuantity, array $snapshot, ?array $passengersSnapshot): string
    {
        $pricingType = trim((string) ($snapshot['pricing_type'] ?? ''));
        $packageUnits = max(1, (int) ($snapshot['quantity'] ?? 1));

        if ($pricingType === 'per_person') {
            $passengerTotal = max(1, (int) ($passengersSnapshot['total'] ?? $billableQuantity));

            if ($packageUnits > 1) {
                return (string) __('account.reservations.price_breakdown.qty_per_person_with_units', [
                    'pax' => $passengerTotal,
                    'units' => $packageUnits,
                    'billable' => $billableQuantity,
                ]);
            }

            return (string) __('account.reservations.price_breakdown.qty_per_person', [
                'pax' => $passengerTotal,
                'billable' => $billableQuantity,
            ]);
        }

        return (string) __('account.reservations.price_breakdown.qty_fixed', [
            'count' => $billableQuantity,
        ]);
    }

    /**
     * @return array{
     *     type: string|null,
     *     value: float|null,
     *     amount: float,
     *     label: string,
     *     amount_formatted: string
     * }|null
     */
    private function resolveAdjustment(
        Booking $booking,
        float $linesSubtotal,
        float $grandTotal,
        ?string $currencyCode,
        int $accountId,
    ): ?array {
        $remarksInternal = is_array($booking->remarks_internal) ? $booking->remarks_internal : [];
        $pricingMeta = is_array($remarksInternal['pricing'] ?? null) ? $remarksInternal['pricing'] : null;

        if ($pricingMeta !== null) {
            $amount = round((float) ($pricingMeta['assignment_adjustment_amount'] ?? 0), 2);
            if (abs($amount) < 0.005) {
                return null;
            }

            $type = isset($pricingMeta['assignment_adjustment_type'])
                ? trim((string) $pricingMeta['assignment_adjustment_type'])
                : null;
            $value = isset($pricingMeta['assignment_adjustment_value'])
                ? (float) $pricingMeta['assignment_adjustment_value']
                : null;

            return [
                'type' => $type !== '' ? $type : null,
                'value' => $value,
                'amount' => $amount,
                'label' => $this->adjustmentLabel($type, $value),
                'amount_formatted' => $this->priceFormatService->formatWithCurrency(
                    $amount,
                    currencyCode: $currencyCode,
                    accountId: $accountId,
                ),
            ];
        }

        $amount = round($grandTotal - $linesSubtotal, 2);
        if (abs($amount) < 0.005) {
            return null;
        }

        return [
            'type' => null,
            'value' => null,
            'amount' => $amount,
            'label' => (string) __('account.reservations.price_breakdown.adjustment_generic'),
            'amount_formatted' => $this->priceFormatService->formatWithCurrency(
                $amount,
                currencyCode: $currencyCode,
                accountId: $accountId,
            ),
        ];
    }

    private function adjustmentLabel(?string $type, ?float $value): string
    {
        if ($type === 'percentage' && $value !== null) {
            return (string) __('account.reservations.price_breakdown.adjustment_percentage', [
                'value' => $this->formatAdjustmentValue($value),
            ]);
        }

        if ($type === 'fixed' && $value !== null) {
            return (string) __('account.reservations.price_breakdown.adjustment_fixed', [
                'value' => $this->formatAdjustmentValue($value),
            ]);
        }

        return (string) __('account.reservations.price_breakdown.adjustment_generic');
    }

    private function formatAdjustmentValue(float $value): string
    {
        $formatted = number_format(abs($value), 2, ',', '.');
        $sign = $value > 0 ? '+' : ($value < 0 ? '−' : '');

        return $sign.$formatted;
    }
}
