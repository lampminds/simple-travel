<?php

namespace App\Services;

use App\Models\CommercialModulePrice;
use App\Models\CommercialModulePriceTier;

/**
 * Computes catalog module monthly amounts from commercial price definitions.
 */
final class ModulePricingCalculator
{
    public function isUserDependent(?CommercialModulePrice $price): bool
    {
        if ($price === null) {
            return false;
        }

        return in_array($price->billing_type, ['hybrid', 'per_user'], true);
    }

    public function monthlyAmount(?CommercialModulePrice $price, int $userCount): ?float
    {
        if ($price === null) {
            return null;
        }

        $userCount = max(1, $userCount);

        return match ($price->billing_type) {
            'fixed', 'usage' => $price->base_price !== null ? (float) $price->base_price : null,
            'per_user' => $this->calculatePerUserPrice($price, $userCount),
            'hybrid' => $this->calculateHybridPrice($price, $userCount),
            default => null,
        };
    }

    public function requiresCustomQuote(?CommercialModulePrice $price, int $userCount): bool
    {
        if ($price === null) {
            return true;
        }

        $userCount = max(1, $userCount);
        $tier = $this->findTierForUserCount($price, $userCount);

        if ($tier !== null && $tier->to_users === null) {
            return $this->monthlyAmount($price, $userCount) === null;
        }

        return $this->monthlyAmount($price, $userCount) === null;
    }

    private function calculatePerUserPrice(CommercialModulePrice $price, int $userCount): ?float
    {
        $base = (float) ($price->base_price ?? 0);
        $perUser = $this->resolvePerUserRate($price, $userCount);

        if ($perUser === null && $price->base_price === null) {
            return null;
        }

        if ($perUser === null) {
            return $price->base_price !== null ? $base : null;
        }

        return $base + ((float) $perUser * $userCount);
    }

    private function calculateHybridPrice(CommercialModulePrice $price, int $userCount): ?float
    {
        $base = (float) ($price->base_price ?? 0);
        $includedUsers = (int) ($price->included_users ?? 0);
        $extraUsers = max(0, $userCount - $includedUsers);

        if ($extraUsers === 0) {
            return $base > 0 || $price->base_price !== null ? $base : null;
        }

        $perUser = $this->resolvePerUserRate($price, $userCount);
        if ($perUser === null) {
            return $base > 0 || $price->base_price !== null ? $base : null;
        }

        return $base + ($extraUsers * (float) $perUser);
    }

    private function resolvePerUserRate(CommercialModulePrice $price, int $userCount): ?float
    {
        $tier = $this->findTierForUserCount($price, $userCount);
        $perUser = $tier?->price_per_user ?? $price->price_per_user;

        return $perUser !== null ? (float) $perUser : null;
    }

    private function findTierForUserCount(CommercialModulePrice $price, int $userCount): ?CommercialModulePriceTier
    {
        foreach ($price->tiers as $tier) {
            $from = $tier->from_users ?? 1;
            $to = $tier->to_users;

            if ($userCount >= $from && ($to === null || $userCount <= $to)) {
                return $tier;
            }
        }

        return null;
    }
}
