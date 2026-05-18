<?php

namespace App\Services;

/**
 * Lookup parameters for {@see CatalogHelperResolver}.
 */
final readonly class CatalogHelperQuery
{
    public function __construct(
        public string $screenCode,
        public string $code,
        public ?int $serviceTypeId = null,
        public ?int $accountTypeId = null,
    ) {}
}
