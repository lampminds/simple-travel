<?php

namespace App\Support;

use App\Services\CatalogHelperQuery;

/**
 * Resolves catalog helper HTML for service wizard step 6.
 *
 * {@code screen_code}: {@see ServiceWizardHelperScreens::STEP6_CONDITIONS}
 * {@code code}: {@code public}, {@code operator}, {@code internal}, {@code is_mandatory}
 */
final class ServiceWizardStep6CatalogHelpers
{
    /** @var list<string> */
    public const VISIBILITY_TAB_KEYS = ['public', 'operator', 'internal'];

    public const IS_MANDATORY_KEY = 'is_mandatory';

    /**
     * @return array<string, string|null> Keys match {@see VISIBILITY_TAB_KEYS}; values are HTML or null.
     */
    public static function htmlMapForForm(?int $serviceTypeId, ?int $accountTypeId): array
    {
        $screenCode = ServiceWizardHelperScreens::STEP6_CONDITIONS;
        $map = [];

        foreach (self::VISIBILITY_TAB_KEYS as $tabKey) {
            $map[$tabKey] = CatalogHelperContent::htmlForQuery(new CatalogHelperQuery(
                screenCode: $screenCode,
                code: $tabKey,
                serviceTypeId: $serviceTypeId,
                accountTypeId: $accountTypeId,
            ));
        }

        return $map;
    }

    public static function htmlForMandatory(?int $serviceTypeId, ?int $accountTypeId): ?string
    {
        return CatalogHelperContent::htmlForQuery(new CatalogHelperQuery(
            screenCode: ServiceWizardHelperScreens::STEP6_CONDITIONS,
            code: self::IS_MANDATORY_KEY,
            serviceTypeId: $serviceTypeId,
            accountTypeId: $accountTypeId,
        ));
    }
}
