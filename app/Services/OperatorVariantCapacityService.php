<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Allocation;
use App\Models\BookingItem;
use App\Models\CatBookingStatus;
use App\Models\ServiceVariant;
use Carbon\Carbon;

/**
 * Computes remaining bookable capacity for an operator on a provider variant (global pool + allocation).
 */
final class OperatorVariantCapacityService
{
    /** @var list<string> */
    private const EXCLUDED_BOOKING_STATUS_CODES = [
        'cancelled',
        'rejected',
        'expired',
    ];

    /** @var list<string> */
    private const EXCLUDED_ITEM_STATUS_CODES = [
        'cancelled',
        'rejected',
        'expired',
        'failed',
    ];

    public function __construct(
        private readonly ServiceVariantAvailabilityResolver $availabilityResolver,
    ) {
    }

    /**
     * @return array{
     *     available: bool,
     *     remaining: int|null,
     *     requested: int,
     *     reason: string|null,
     *     capacity_limit: int|null
     * }
     */
    public function assessBookingQuantity(
        ServiceVariant $variant,
        int $operatorId,
        int $providerId,
        Carbon $date,
        int $requestedQuantity,
        ?int $excludeBookingId = null,
    ): array {
        $resolution = $this->availabilityResolver->resolveForDate($variant, $date);

        if (! $resolution->available) {
            return $this->deny($requestedQuantity, $resolution->closed ? 'closed' : 'unavailable', 0);
        }

        $allocation = $this->activeAllocation($providerId, $operatorId, (int) $variant->id, $date);

        if ($allocation === null) {
            return $this->deny($requestedQuantity, 'no_allocation', 0);
        }

        if ($resolution->isUnlimited()) {
            return $this->assessWithOperatorPool(null, $allocation, $operatorId, (int) $variant->id, $date, $requestedQuantity, $excludeBookingId);
        }

        $globalLimit = (int) ($resolution->capacity ?? 0);
        $globalBooked = $this->bookedQuantityForVariantOnDate((int) $variant->id, $date, $excludeBookingId);
        $globalRemaining = max(0, $globalLimit - $globalBooked);

        if ($requestedQuantity > $globalRemaining) {
            return $this->deny($requestedQuantity, 'global_exhausted', $globalRemaining);
        }

        return $this->assessWithOperatorPool(
            $globalRemaining,
            $allocation,
            $operatorId,
            (int) $variant->id,
            $date,
            $requestedQuantity,
            $excludeBookingId,
        );
    }

    /**
     * @return array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}
     */
    private function assessWithOperatorPool(
        ?int $globalRemaining,
        Allocation $allocation,
        int $operatorId,
        int $variantId,
        Carbon $date,
        int $requestedQuantity,
        ?int $excludeBookingId,
    ): array {
        return match ($allocation->allocation_type) {
            Allocation::TYPE_FREE_SALE => $this->allow(
                $requestedQuantity,
                $globalRemaining,
            ),
            Allocation::TYPE_SOFT => $this->allow(
                $requestedQuantity,
                $globalRemaining,
            ),
            Allocation::TYPE_HARD => $this->assessHardAllocation(
                $allocation,
                $operatorId,
                $variantId,
                $date,
                $requestedQuantity,
                $globalRemaining,
                $excludeBookingId,
            ),
            default => $this->deny($requestedQuantity, 'no_allocation', 0),
        };
    }

    /**
     * @return array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}
     */
    private function assessHardAllocation(
        Allocation $allocation,
        int $operatorId,
        int $variantId,
        Carbon $date,
        int $requestedQuantity,
        ?int $globalRemaining,
        ?int $excludeBookingId,
    ): array {
        $operatorLimit = (int) $allocation->capacity;
        $operatorBooked = $this->bookedQuantityForOperatorVariantOnDate(
            $operatorId,
            $variantId,
            $date,
            $excludeBookingId,
        );
        $operatorRemaining = max(0, $operatorLimit - $operatorBooked);

        $effectiveRemaining = $globalRemaining === null
            ? $operatorRemaining
            : min($globalRemaining, $operatorRemaining);

        if ($requestedQuantity > $effectiveRemaining) {
            return $this->deny($requestedQuantity, 'operator_exhausted', $effectiveRemaining);
        }

        return $this->allow($requestedQuantity, $effectiveRemaining);
    }

    public function bookedQuantityForVariantOnDate(
        int $serviceVariantId,
        Carbon $date,
        ?int $excludeBookingId = null,
    ): int {
        return $this->sumBookedQuantity($serviceVariantId, null, $date, $excludeBookingId);
    }

    public function bookedQuantityForOperatorVariantOnDate(
        int $operatorId,
        int $serviceVariantId,
        Carbon $date,
        ?int $excludeBookingId = null,
    ): int {
        return $this->sumBookedQuantity($serviceVariantId, $operatorId, $date, $excludeBookingId);
    }

    private function sumBookedQuantity(
        int $serviceVariantId,
        ?int $operatorId,
        Carbon $date,
        ?int $excludeBookingId,
    ): int {
        $day = $date->toDateString();

        $query = BookingItem::query()
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->join('operator_package_items', 'operator_package_items.id', '=', 'booking_items.operator_package_item_id')
            ->join('cat_booking_statuses as booking_status', 'booking_status.id', '=', 'bookings.status_id')
            ->join('cat_booking_statuses as item_status', 'item_status.id', '=', 'booking_items.status_id')
            ->where('operator_package_items.service_variant_id', $serviceVariantId)
            ->whereDate('booking_items.start_date', '<=', $day)
            ->whereDate('booking_items.end_date', '>=', $day)
            ->where('booking_status.type', CatBookingStatus::TYPE_MAIN)
            ->where('item_status.type', CatBookingStatus::TYPE_ITEM)
            ->whereNotIn('booking_status.code', self::EXCLUDED_BOOKING_STATUS_CODES)
            ->whereNotIn('item_status.code', self::EXCLUDED_ITEM_STATUS_CODES);

        if ($operatorId !== null) {
            $query->where('bookings.operator_id', $operatorId);
        }

        if ($excludeBookingId !== null) {
            $query->where('bookings.id', '!=', $excludeBookingId);
        }

        return (int) $query->sum('booking_items.quantity');
    }

    private function activeAllocation(
        int $providerId,
        int $operatorId,
        int $variantId,
        Carbon $date,
    ): ?Allocation {
        $day = $date->toDateString();

        /** @var \Illuminate\Support\Collection<int, Allocation> $candidates */
        $candidates = Allocation::query()
            ->where('provider_id', $providerId)
            ->where('operator_id', $operatorId)
            ->where('service_variant_id', $variantId)
            ->where('active', true)
            ->orderByDesc('id')
            ->get();

        foreach ($candidates as $allocation) {
            $start = $allocation->start_date?->format('Y-m-d') ?? '0001-01-01';
            $end = $allocation->end_date?->format('Y-m-d') ?? '9999-12-31';

            if ($day >= $start && $day <= $end) {
                return $allocation;
            }
        }

        return null;
    }

    /**
     * @return array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}
     */
    private function allow(int $requestedQuantity, ?int $remaining): array
    {
        return [
            'available' => true,
            'remaining' => $remaining,
            'requested' => $requestedQuantity,
            'reason' => null,
            'capacity_limit' => $remaining,
        ];
    }

    /**
     * @return array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}
     */
    private function deny(int $requestedQuantity, string $reason, ?int $remaining): array
    {
        return [
            'available' => false,
            'remaining' => $remaining,
            'requested' => $requestedQuantity,
            'reason' => $reason,
            'capacity_limit' => $remaining,
        ];
    }
}
