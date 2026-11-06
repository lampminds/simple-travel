<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\OperatorPackageItem;
use Illuminate\Validation\ValidationException;

/**
 * Passenger data stored on bookings.passengers_snapshot.
 *
 * New bookings store ages. Older bookings keep adult/child/infant/senior counts.
 */
final class BookingPassengersSnapshot
{
    public const MAX_PASSENGERS = 30;

    public const MAX_AGE = 120;

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
        return [
            'ages' => ['required', 'array', 'min:1', 'max:'.self::MAX_PASSENGERS],
            'ages.*' => ['required', 'integer', 'min:0', 'max:'.self::MAX_AGE],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function validationAttributes(): array
    {
        return [
            'ages' => (string) __('account.reservations.passengers_heading'),
            'ages.*' => (string) __('account.reservations.field_age'),
        ];
    }

    /**
     * @param  list<int|string>  $ages
     * @return array{ages: list<int>, total: int}
     */
    public static function fromAges(array $ages): array
    {
        $normalized = [];

        foreach (array_values($ages) as $age) {
            $normalized[] = max(0, min(self::MAX_AGE, (int) $age));
        }

        return [
            'ages' => $normalized,
            'total' => count($normalized),
        ];
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
        if (self::headcount($snapshot) < 1) {
            throw ValidationException::withMessages([
                'ages' => (string) __('account.reservations.validation.passengers_required'),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>|null  $snapshot
     */
    public static function headcount(?array $snapshot): int
    {
        if ($snapshot === null) {
            return 0;
        }

        if (isset($snapshot['ages']) && is_array($snapshot['ages'])) {
            return count($snapshot['ages']);
        }

        if (array_key_exists('total', $snapshot) && ! self::hasTypeCounts($snapshot)) {
            return max(0, (int) $snapshot['total']);
        }

        $sum = 0;
        foreach (self::types() as $type) {
            $sum += max(0, (int) ($snapshot[$type] ?? 0));
        }

        return $sum;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private static function hasTypeCounts(array $snapshot): bool
    {
        foreach (self::types() as $type) {
            if (array_key_exists($type, $snapshot)) {
                return true;
            }
        }

        return false;
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
            $passengerTotal = $passengersSnapshot === null
                ? 1
                : max(1, self::headcount($passengersSnapshot));

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

        if (isset($snapshot['ages']) && is_array($snapshot['ages']) && $snapshot['ages'] !== []) {
            $ages = array_map(static fn (mixed $age): int => (int) $age, $snapshot['ages']);

            return (string) trans_choice('account.reservations.passenger_ages_summary', count($ages), [
                'count' => count($ages),
                'ages' => implode(', ', $ages),
            ]);
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
