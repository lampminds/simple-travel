<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;

final class AllocationValidationService
{
    /**
     * Accepted-offer variant targets eligible for allocation to an operator.
     *
     * @return array{variants: array<int, string>}
     */
    public function eligibleTargetOptions(int $providerId, int $operatorId): array
    {
        $offers = ServiceOffer::query()
            ->where('provider_id', $providerId)
            ->where('operator_id', $operatorId)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->with([
                'serviceVariant.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
            ])
            ->get();

        $variants = [];

        foreach ($offers as $offer) {
            $variant = $offer->serviceVariant;
            if ($variant === null) {
                continue;
            }
            $variants[(int) $variant->id] = $this->variantLabel($variant);
        }

        asort($variants);

        return [
            'variants' => $variants,
        ];
    }

    public function targetHasAcceptedOffer(int $providerId, int $operatorId, ?int $serviceVariantId): bool
    {
        if ($serviceVariantId === null || $serviceVariantId <= 0) {
            return false;
        }

        return ServiceOffer::query()
            ->where('provider_id', $providerId)
            ->where('operator_id', $operatorId)
            ->where('service_variant_id', $serviceVariantId)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->exists();
    }

    /**
     * @return array{service_variant_id: int}|null
     */
    public function parseTargetKey(string $targetKey): ?array
    {
        $targetKey = trim($targetKey);
        if (preg_match('/^variant:(\d+)$/', $targetKey, $matches) === 1) {
            return [
                'service_variant_id' => (int) $matches[1],
            ];
        }

        return null;
    }

    public function targetKeyFromAllocation(Allocation $allocation): string
    {
        return 'variant:'.(int) $allocation->service_variant_id;
    }

    public function findOverlappingAllocation(
        int $providerId,
        int $operatorId,
        int $serviceVariantId,
        ?string $startDate,
        ?string $endDate,
        ?int $excludeAllocationId = null,
    ): ?Allocation {
        $query = Allocation::query()
            ->where('provider_id', $providerId)
            ->where('operator_id', $operatorId)
            ->where('service_variant_id', $serviceVariantId);

        if ($excludeAllocationId !== null) {
            $query->where('id', '!=', $excludeAllocationId);
        }

        /** @var \Illuminate\Support\Collection<int, Allocation> $candidates */
        $candidates = $query->get();

        foreach ($candidates as $existing) {
            if ($this->dateRangesOverlap(
                $startDate,
                $endDate,
                $existing->start_date?->format('Y-m-d'),
                $existing->end_date?->format('Y-m-d'),
            )) {
                return $existing;
            }
        }

        return null;
    }

    public function allocationTargetLabel(Allocation $allocation): string
    {
        $variant = $allocation->serviceVariant;
        if ($variant instanceof ServiceVariant) {
            return $this->variantLabel($variant);
        }

        return __('account.allocations.target_variant_fallback', ['id' => $allocation->service_variant_id]);
    }

    private function variantLabel(ServiceVariant $variant): string
    {
        $serviceName = trim($variant->service?->name ?? '');
        $sku = trim((string) $variant->sku);

        $serviceChunk = $serviceName !== '' ? $serviceName : ('Service #'.$variant->service_id);
        $skuChunk = $sku !== '' ? $sku : ('Variant #'.$variant->id);

        return $serviceChunk.' — '.$skuChunk;
    }

    private function dateRangesOverlap(?string $startA, ?string $endA, ?string $startB, ?string $endB): bool
    {
        $minDate = '0001-01-01';
        $maxDate = '9999-12-31';

        $startA = $startA ?? $minDate;
        $endA = $endA ?? $maxDate;
        $startB = $startB ?? $minDate;
        $endB = $endB ?? $maxDate;

        if ($startA > $endA || $startB > $endB) {
            return false;
        }

        return $startA <= $endB && $startB <= $endA;
    }
}
