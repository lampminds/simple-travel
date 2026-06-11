<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\OperatorPackageItem;
use Illuminate\Validation\ValidationException;

/**
 * Normalizes passenger-type counts stored on bookings.passengers_snapshot.
 */
final class BookingPassengersSnapshot
{
    public const TYPE_ADULT = 'adult';

    public const TYPE_CHILD = 'child';

    public const TYPE_INFANT = 'infant';

    public const TYPE_SENIOR = 'senior';

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_ADULT,
            self::TYPE_CHILD,
            self::TYPE_INFANT,
            self::TYPE_SENIOR,
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function validationRules(): array
    {
        $rules = [];

        foreach (self::types() as $type) {
            $rules["passengers.{$type}"] = ['required', 'integer', 'min:0', 'max:999'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public static function validationAttributes(): array
    {
        $attributes = [];

        foreach (self::types() as $type) {
            $attributes["passengers.{$type}"] = (string) __('account.reservations.passenger_types.'.$type);
        }

        return $attributes;
    }

    /**
     * @param  array<string, mixed>  $counts
     * @return array{adult: int, child: int, infant: int, senior: int, total: int}
     */
    public static function normalize(array $counts): array
    {
        $snapshot = [];

        foreach (self::types() as $type) {
            $snapshot[$type] = max(0, (int) ($counts[$type] ?? 0));
        }

        $snapshot['total'] = array_sum($snapshot);

        return $snapshot;
    }

    /**
     * @param  array{adult: int, child: int, infant: int, senior: int, total: int}  $snapshot
     */
    public static function assertHasPassengers(array $snapshot): void
    {
        if (($snapshot['total'] ?? 0) < 1) {
            throw ValidationException::withMessages([
                'passengers' => (string) __('account.reservations.validation.passengers_required'),
            ]);
        }
    }

    /**
     * Resolves billable quantity for a package line.
     *
     * per_person lines multiply package units by passenger total; other pricing types
     * use only the package item quantity.
     *
     * @param  array{total?: int}|null  $passengersSnapshot
     */
    public static function lineQuantity(OperatorPackageItem $packageItem, ?array $passengersSnapshot): int
    {
        $packageQuantity = max(1, (int) $packageItem->quantity);
        $pricingType = $packageItem->serviceVariant?->pricing_type;

        if ($pricingType === 'per_person') {
            $passengerTotal = max(1, (int) ($passengersSnapshot['total'] ?? 1));

            return $packageQuantity * $passengerTotal;
        }

        return $packageQuantity;
    }

    /**
     * @param  array<string, int>|null  $snapshot
     */
    public static function formatSummary(?array $snapshot): string
    {
        if ($snapshot === null) {
            return '—';
        }

        $normalized = self::normalize($snapshot);

        if ($normalized['total'] < 1) {
            return '—';
        }

        $parts = [];

        foreach (self::types() as $type) {
            $count = $normalized[$type];
            if ($count > 0) {
                $parts[] = trans_choice(
                    'account.reservations.passenger_types_count.'.$type,
                    $count,
                    ['count' => $count],
                );
            }
        }

        return $parts !== [] ? implode(', ', $parts) : '—';
    }
}
