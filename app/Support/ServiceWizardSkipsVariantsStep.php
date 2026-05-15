<?php

namespace App\Support;

/**
 * Service types that skip wizard step 4 (sellable variants).
 */
final class ServiceWizardSkipsVariantsStep
{
    /** @var list<string> */
    public const SERVICE_TYPE_CODES = ['transfer'];

    public static function isSkippedForServiceTypeCode(?string $code): bool
    {
        $code = $code === null ? '' : strtolower(trim($code));

        return $code !== '' && in_array($code, self::SERVICE_TYPE_CODES, true);
    }
}
