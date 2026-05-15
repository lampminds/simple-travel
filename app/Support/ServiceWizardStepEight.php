<?php

namespace App\Support;

/**
 * Service types that expose wizard step 8 (vertical-specific advanced profile).
 */
final class ServiceWizardStepEight
{
    /** @var list<string> */
    public const SERVICE_TYPE_CODES = ['gastronomy', 'accomodation', 'activity', 'event', 'transfer'];

    public static function isEnabledForServiceTypeCode(?string $code): bool
    {
        $code = $code === null ? '' : strtolower(trim($code));

        return $code !== '' && in_array($code, self::SERVICE_TYPE_CODES, true);
    }
}
