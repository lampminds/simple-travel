<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\CatBookingStatus;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageAllocation;
use Carbon\Carbon;

/**
 * Computes remaining bookable capacity for an agency on an operator package (global pool + allocation).
 */
final class AgencyPackageCapacityService
{
    /** @var list<string> */
    private const EXCLUDED_BOOKING_STATUS_CODES = [
        'cancelled',
        'rejected',
        'expired',
    ];

    public function __construct(
        private readonly OperatorPackageAvailabilityResolver $availabilityResolver,
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
        OperatorServiceCatalog $catalog,
        int $agencyId,
        int $operatorId,
        Carbon $date,
        int $requestedQuantity,
        ?int $excludeBookingId = null,
    ): array {
        $resolution = $this->availabilityResolver->resolveForDate($catalog, $date);

        if (! $resolution->available) {
            return $this->deny($requestedQuantity, $resolution->closed ? 'closed' : 'unavailable', 0);
        }

        $allocation = $this->activeAllocation($operatorId, $agencyId, (int) $catalog->id, $date);

        if ($allocation === null) {
            return $this->deny($requestedQuantity, 'no_allocation', 0);
        }

        if ($resolution->isUnlimited()) {
            return $this->assessWithAgencyPool(null, $allocation, $agencyId, (int) $catalog->id, $operatorId, $date, $requestedQuantity, $excludeBookingId);
        }

        $globalLimit = (int) ($resolution->capacity ?? 0);
        $globalBooked = $this->bookedQuantityForCatalogOnDate((int) $catalog->id, $operatorId, $date, $excludeBookingId);
        $globalRemaining = max(0, $globalLimit - $globalBooked);

        if ($requestedQuantity > $globalRemaining) {
            return $this->deny($requestedQuantity, 'global_exhausted', $globalRemaining);
        }

        return $this->assessWithAgencyPool(
            $globalRemaining,
            $allocation,
            $agencyId,
            (int) $catalog->id,
            $operatorId,
            $date,
            $requestedQuantity,
            $excludeBookingId,
        );
    }

    /**
     * @return array{available: bool, remaining: int|null, requested: int, reason: string|null, capacity_limit: int|null}
     */
    private function assessWithAgencyPool(
        ?int $globalRemaining,
        PackageAllocation $allocation,
        int $agencyId,
        int $catalogId,
        int $operatorId,
        Carbon $date,
        int $requestedQuantity,
        ?int $excludeBookingId,
    ): array {
        return match ($allocation->allocation_type) {
            PackageAllocation::TYPE_FREE_SALE => $this->allow(
                $requestedQuantity,
                $globalRemaining,
            ),
            PackageAllocation::TYPE_SOFT => $this->allow(
                $requestedQuantity,
                $globalRemaining,
            ),
            PackageAllocation::TYPE_HARD => $this->assessHardAllocation(
                $allocation,
                $agencyId,
                $catalogId,
                $operatorId,
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
        PackageAllocation $allocation,
        int $agencyId,
        int $catalogId,
        int $operatorId,
        Carbon $date,
        int $requestedQuantity,
        ?int $globalRemaining,
        ?int $excludeBookingId,
    ): array {
        $agencyLimit = (int) $allocation->capacity;
        $agencyBooked = $this->bookedQuantityForAgencyCatalogOnDate(
            $agencyId,
            $catalogId,
            $operatorId,
            $date,
            $excludeBookingId,
        );
        $agencyRemaining = max(0, $agencyLimit - $agencyBooked);

        $effectiveRemaining = $globalRemaining === null
            ? $agencyRemaining
            : min($globalRemaining, $agencyRemaining);

        if ($requestedQuantity > $effectiveRemaining) {
            return $this->deny($requestedQuantity, 'agency_exhausted', $effectiveRemaining);
        }

        return $this->allow($requestedQuantity, $effectiveRemaining);
    }

    public function bookedQuantityForCatalogOnDate(
        int $catalogId,
        int $operatorId,
        Carbon $date,
        ?int $excludeBookingId = null,
    ): int {
        return $this->sumBookedQuantity($catalogId, $operatorId, null, $date, $excludeBookingId);
    }

    public function bookedQuantityForAgencyCatalogOnDate(
        int $agencyId,
        int $catalogId,
        int $operatorId,
        Carbon $date,
        ?int $excludeBookingId = null,
    ): int {
        return $this->sumBookedQuantity($catalogId, $operatorId, $agencyId, $date, $excludeBookingId);
    }

    private function sumBookedQuantity(
        int $catalogId,
        int $operatorId,
        ?int $agencyId,
        Carbon $date,
        ?int $excludeBookingId,
    ): int {
        $day = $date->toDateString();

        $query = Booking::query()
            ->join('package_offers', 'package_offers.id', '=', 'bookings.package_offer_id')
            ->join('cat_booking_statuses as booking_status', 'booking_status.id', '=', 'bookings.status_id')
            ->where('package_offers.operator_service_catalog_id', $catalogId)
            ->where('bookings.operator_id', $operatorId)
            ->whereDate('bookings.travel_start_date', '<=', $day)
            ->whereDate('bookings.travel_end_date', '>=', $day)
            ->where('booking_status.type', CatBookingStatus::TYPE_MAIN)
            ->whereNotIn('booking_status.code', self::EXCLUDED_BOOKING_STATUS_CODES);

        if ($agencyId !== null) {
            $query->where('bookings.agency_id', $agencyId);
        }

        if ($excludeBookingId !== null) {
            $query->where('bookings.id', '!=', $excludeBookingId);
        }

        return (int) $query->count('bookings.id');
    }

    private function activeAllocation(
        int $operatorId,
        int $agencyId,
        int $catalogId,
        Carbon $date,
    ): ?PackageAllocation {
        $day = $date->toDateString();

        /** @var \Illuminate\Support\Collection<int, PackageAllocation> $candidates */
        $candidates = PackageAllocation::query()
            ->where('operator_id', $operatorId)
            ->where('agency_id', $agencyId)
            ->where('operator_service_catalog_id', $catalogId)
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
