<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageItem;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageOffer;
use App\Models\ServiceVariant;
use App\Support\BookingPassengersSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Validates package booking lines against variant availability, allocations, and existing bookings.
 */
final class BookingAvailabilityValidationService
{
    public function __construct(
        private readonly OperatorVariantCapacityService $capacityService,
        private readonly AgencyPackageCapacityService $packageCapacityService,
    ) {
    }

    public function assertPackageOfferAvailable(
        PackageOffer $offer,
        Carbon $travelStart,
        Carbon $travelEnd,
    ): void {
        $catalog = $offer->catalog;
        if ($catalog === null) {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.offer_not_bookable'),
            ]);
        }

        $catalog->loadMissing(['availabilityRules.timeSlots', 'availabilityOverrides']);

        $operatorId = (int) $offer->operator_id;
        $agencyId = (int) $offer->agency_id;
        $requestedQuantity = 1;

        $inventoryType = $catalog->inventory_type ?? 'unlimited';

        if (in_array($inventoryType, ['per_day', 'per_timeslot', 'per_departure'], true)) {
            for ($day = $travelStart->copy(); $day->lte($travelEnd); $day->addDay()) {
                $assessment = $this->packageCapacityService->assessBookingQuantity(
                    $catalog,
                    $agencyId,
                    $operatorId,
                    $day,
                    $requestedQuantity,
                );

                if (! $assessment['available']) {
                    throw ValidationException::withMessages([
                        'travel_start_date' => $this->messageForPackageAssessment($catalog, $day, $assessment),
                    ]);
                }
            }

            return;
        }

        if ($inventoryType === 'unlimited') {
            $assessment = $this->packageCapacityService->assessBookingQuantity(
                $catalog,
                $agencyId,
                $operatorId,
                $travelStart,
                $requestedQuantity,
            );

            if (! $assessment['available']) {
                throw ValidationException::withMessages([
                    'travel_start_date' => $this->messageForPackageAssessment($catalog, $travelStart, $assessment),
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, OperatorPackageItem>  $includedItems
     * @param  array{adult: int, child: int, infant: int, senior: int, total: int}  $passengersSnapshot
     */
    public function assertPackageItemsAvailable(
        int $operatorId,
        Collection $includedItems,
        Carbon $travelStart,
        Carbon $travelEnd,
        array $passengersSnapshot,
    ): void {
        foreach ($includedItems as $packageItem) {
            $variant = $packageItem->serviceVariant;
            if (! $variant instanceof ServiceVariant) {
                continue;
            }

            $providerId = (int) ($packageItem->serviceOffer?->provider_id ?? 0);
            if ($providerId <= 0) {
                continue;
            }

            $variant->loadMissing([
                'service.availabilityRules',
                'service.availabilityOverrides',
                'availabilityRules.timeSlots',
                'availabilityOverrides',
            ]);

            $quantity = BookingPassengersSnapshot::lineQuantity($packageItem, $passengersSnapshot);
            $this->assertVariantQuantityConstraints($variant, $quantity);

            $itemStart = $this->resolveItemStartDate($packageItem, $travelStart, $travelEnd);
            $itemEnd = $this->resolveItemEndDate($packageItem, $variant, $travelStart, $travelEnd);

            if ($variant->inventory_type === 'per_day' || $variant->inventory_type === 'per_timeslot' || $variant->inventory_type === 'per_departure') {
                for ($day = $itemStart->copy(); $day->lte($itemEnd); $day->addDay()) {
                    $this->assertAdvanceBookingWindow($variant, $day);

                    $assessment = $this->capacityService->assessBookingQuantity(
                        $variant,
                        $operatorId,
                        $providerId,
                        $day,
                        $quantity,
                    );

                    if (! $assessment['available']) {
                        throw ValidationException::withMessages([
                            'travel_start_date' => $this->messageForAssessment($variant, $packageItem, $day, $assessment),
                        ]);
                    }
                }

                continue;
            }

            if ($variant->inventory_type === 'unlimited') {
                $this->assertAdvanceBookingWindow($variant, $itemStart);

                $assessment = $this->capacityService->assessBookingQuantity(
                    $variant,
                    $operatorId,
                    $providerId,
                    $itemStart,
                    $quantity,
                );

                if (! $assessment['available']) {
                    throw ValidationException::withMessages([
                        'travel_start_date' => $this->messageForAssessment($variant, $packageItem, $itemStart, $assessment),
                    ]);
                }
            }
        }
    }

    private function assertVariantQuantityConstraints(ServiceVariant $variant, int $quantity): void
    {
        if ($variant->capacity_min !== null && $quantity < (int) $variant->capacity_min) {
            throw ValidationException::withMessages([
                'passengers' => __('account.reservations.validation.quantity_below_minimum', [
                    'variant' => $this->variantLabel($variant),
                    'min' => (int) $variant->capacity_min,
                ]),
            ]);
        }

        if ($variant->capacity_max !== null && $quantity > (int) $variant->capacity_max) {
            throw ValidationException::withMessages([
                'passengers' => __('account.reservations.validation.quantity_above_maximum', [
                    'variant' => $this->variantLabel($variant),
                    'max' => (int) $variant->capacity_max,
                ]),
            ]);
        }
    }

    private function assertAdvanceBookingWindow(ServiceVariant $variant, Carbon $serviceDate): void
    {
        $now = Carbon::now();
        $serviceDay = $serviceDate->copy()->startOfDay();

        if ($variant->min_advance_booking_hours !== null && (int) $variant->min_advance_booking_hours > 0) {
            $earliest = $now->copy()->addHours((int) $variant->min_advance_booking_hours);
            if ($serviceDay->lt($earliest->startOfDay())) {
                throw ValidationException::withMessages([
                    'travel_start_date' => __('account.reservations.validation.advance_booking_too_soon', [
                        'variant' => $this->variantLabel($variant),
                        'hours' => (int) $variant->min_advance_booking_hours,
                    ]),
                ]);
            }
        }

        if ($variant->max_advance_booking_days !== null && (int) $variant->max_advance_booking_days > 0) {
            $latest = $now->copy()->addDays((int) $variant->max_advance_booking_days)->startOfDay();
            if ($serviceDay->gt($latest)) {
                throw ValidationException::withMessages([
                    'travel_start_date' => __('account.reservations.validation.advance_booking_too_far', [
                        'variant' => $this->variantLabel($variant),
                        'days' => (int) $variant->max_advance_booking_days,
                    ]),
                ]);
            }
        }
    }

    private function resolveItemStartDate(
        OperatorPackageItem $item,
        Carbon $travelStart,
        Carbon $travelEnd,
    ): Carbon {
        $dayNumber = (int) ($item->day_number ?? 0);
        if ($dayNumber > 0) {
            $offset = max(0, $dayNumber - 1);
            $date = $travelStart->copy()->addDays($offset);
            if ($date->gt($travelEnd)) {
                return $travelEnd->copy();
            }

            return $date;
        }

        return $travelStart->copy();
    }

    private function resolveItemEndDate(
        OperatorPackageItem $item,
        ServiceVariant $variant,
        Carbon $travelStart,
        Carbon $travelEnd,
    ): Carbon {
        if ((int) ($item->day_number ?? 0) > 0) {
            return $this->resolveItemStartDate($item, $travelStart, $travelEnd);
        }

        if ($variant->inventory_type === 'per_day') {
            return $travelEnd->copy();
        }

        return $travelEnd->copy();
    }

    /**
     * @param  array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}  $assessment
     */
    private function messageForAssessment(
        ServiceVariant $variant,
        OperatorPackageItem $packageItem,
        Carbon $date,
        array $assessment,
    ): string {
        $label = $this->variantLabel($variant);
        $dateLabel = locale_date($date);
        $requested = number_format((int) $assessment['requested']);
        $remaining = $assessment['capacity_limit'] !== null
            ? number_format((int) $assessment['capacity_limit'])
            : '—';

        return match ($assessment['reason']) {
            'closed' => __('account.reservations.validation.variant_closed_on_date', [
                'variant' => $label,
                'date' => $dateLabel,
            ]),
            'unavailable' => __('account.reservations.validation.variant_unavailable_on_date', [
                'variant' => $label,
                'date' => $dateLabel,
            ]),
            'no_allocation' => __('account.reservations.validation.variant_no_allocation', [
                'variant' => $label,
                'date' => $dateLabel,
            ]),
            'global_exhausted', 'operator_exhausted' => __('account.reservations.validation.variant_capacity_exhausted', [
                'variant' => $label,
                'date' => $dateLabel,
                'requested' => $requested,
                'remaining' => $remaining,
            ]),
            default => __('account.reservations.validation.variant_not_bookable', [
                'variant' => $label,
                'date' => $dateLabel,
            ]),
        };
    }

    /**
     * @param  array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}  $assessment
     */
    private function messageForPackageAssessment(
        OperatorServiceCatalog $catalog,
        Carbon $date,
        array $assessment,
    ): string {
        $label = $catalog->displayLabel();
        $dateLabel = locale_date($date);
        $requested = number_format((int) $assessment['requested']);
        $remaining = $assessment['capacity_limit'] !== null
            ? number_format((int) $assessment['capacity_limit'])
            : '—';

        return match ($assessment['reason']) {
            'closed' => __('account.reservations.validation.package_closed_on_date', [
                'package' => $label,
                'date' => $dateLabel,
            ]),
            'unavailable' => __('account.reservations.validation.package_unavailable_on_date', [
                'package' => $label,
                'date' => $dateLabel,
            ]),
            'no_allocation' => __('account.reservations.validation.package_no_allocation', [
                'package' => $label,
                'date' => $dateLabel,
            ]),
            'global_exhausted', 'agency_exhausted' => __('account.reservations.validation.package_capacity_exhausted', [
                'package' => $label,
                'date' => $dateLabel,
                'requested' => $requested,
                'remaining' => $remaining,
            ]),
            default => __('account.reservations.validation.package_not_bookable', [
                'package' => $label,
                'date' => $dateLabel,
            ]),
        };
    }

    private function variantLabel(ServiceVariant $variant): string
    {
        $name = trim($variant->name ?? '');
        $sku = trim((string) $variant->sku);

        if ($name !== '') {
            return $sku !== '' ? "{$name} ({$sku})" : $name;
        }

        return $sku !== '' ? $sku : ('#'.$variant->id);
    }
}
