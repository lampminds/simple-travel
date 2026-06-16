<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Result of resolving variant availability for a single date (and optional time slot).
 */
final readonly class AvailabilityResolution
{
    public function __construct(
        public bool $available,
        public ?int $capacity,
        public bool $closed = false,
    ) {
    }

    public function isUnlimited(): bool
    {
        return $this->available && $this->capacity === null;
    }
}
