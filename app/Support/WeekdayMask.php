<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Bitmask helpers for service_variant_availability_rules.weekday_mask.
 *
 * @see docs/service-availability-model.md
 */
final class WeekdayMask
{
    /** @var array<int, string> bit => translation key suffix */
    public const DAY_BITS = [
        1 => 'mon',
        2 => 'tue',
        4 => 'wed',
        8 => 'thu',
        16 => 'fri',
        32 => 'sat',
        64 => 'sun',
    ];

    /**
     * @param  list<int|string>  $selectedBits  Checked weekday bit values (1, 2, 4, …).
     */
    public static function fromSelectedBits(array $selectedBits): ?int
    {
        $mask = 0;
        foreach ($selectedBits as $bit) {
            $bit = (int) $bit;
            if ($bit <= 0 || ! isset(self::DAY_BITS[$bit])) {
                continue;
            }
            $mask |= $bit;
        }

        if ($mask === 0 || $mask === 127) {
            return null;
        }

        return $mask;
    }

    /**
     * @return list<int>
     */
    public static function toSelectedBits(?int $mask): array
    {
        if ($mask === null || $mask === 0) {
            return array_keys(self::DAY_BITS);
        }

        $selected = [];
        foreach (self::DAY_BITS as $bit => $_) {
            if (($mask & $bit) === $bit) {
                $selected[] = $bit;
            }
        }

        return $selected === [] ? array_keys(self::DAY_BITS) : $selected;
    }

    public static function label(?int $mask): string
    {
        if ($mask === null || $mask === 0 || $mask === 127) {
            return (string) __('account.availability.weekdays.all');
        }

        $parts = [];
        foreach (self::DAY_BITS as $bit => $suffix) {
            if (($mask & $bit) === $bit) {
                $parts[] = (string) __('account.availability.weekdays.'.$suffix);
            }
        }

        return $parts !== [] ? implode(', ', $parts) : (string) __('account.availability.weekdays.all');
    }
}
