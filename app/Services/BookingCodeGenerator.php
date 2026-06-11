<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;

/**
 * Generates unique booking codes in BK-YY-AAnnA format.
 */
final class BookingCodeGenerator
{
    private const LETTERS = 'ABCDEFGHJKMNPQRTWXYZ';

    private const DIGITS = '2346789';

    public function generate(): string
    {
        do {
            $code = $this->build();
        } while (Booking::query()->where('booking_code', $code)->exists());

        return $code;
    }

    private function build(): string
    {
        $year = now()->format('y');
        $letterOne = self::LETTERS[random_int(0, strlen(self::LETTERS) - 1)];
        $digits = self::DIGITS[random_int(0, strlen(self::DIGITS) - 1)]
            .self::DIGITS[random_int(0, strlen(self::DIGITS) - 1)];
        $letterTwo = self::LETTERS[random_int(0, strlen(self::LETTERS) - 1)];

        return "BK-{$year}-{$letterOne}{$digits}{$letterTwo}";
    }
}
