<?php

declare(strict_types=1);

namespace App\Support;

/**
 * One booking question after merging every included service of a package.
 */
final class ResolvedBookingField
{
    public function __construct(
        public readonly int $id,
        public readonly string $scope,
        public readonly string $inputType,
        public readonly bool $required,
        public readonly int $sortOrder,
        public readonly string $label,
        public readonly ?string $help,
    ) {
    }
}
