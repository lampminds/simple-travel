<?php

namespace App\Support;

use App\Services\CatalogHelperQuery;

/**
 * Resolves catalog helper HTML for service wizard variant form fields.
 *
 * {@code screen_code}: {@see ServiceWizardHelperScreens::STEP4_VARIANTS}
 * {@code code}: {@code catalog_variant_} + DB column (or translation column / media collection).
 */
final class ServiceWizardVariantCatalogHelpers
{
    /** @var list<string> */
    public const FORM_FIELD_KEYS = [
        'sku',
        'status',
        'inventory_type',
        'inventory_total',
        'capacity_min',
        'capacity_max',
        'min_advance_booking_hours',
        'max_advance_booking_days',
        'start_time',
        'end_time',
        'pricing_type',
        'base_price',
        'currency_id',
        'name',
        'description',
        'main',
        'gallery',
    ];

    public static function helperCode(string $fieldKey): string
    {
        $suffix = match ($fieldKey) {
            'currency_id' => 'currency',
            default => $fieldKey,
        };

        return 'catalog_variant_'.$suffix;
    }

    /**
     * @return array<string, string|null> Keys match {@see FORM_FIELD_KEYS}; values are HTML or null.
     */
    public static function htmlMapForForm(?int $serviceTypeId, ?int $accountTypeId): array
    {
        $screenCode = ServiceWizardHelperScreens::STEP4_VARIANTS;
        $map = [];

        foreach (self::FORM_FIELD_KEYS as $fieldKey) {
            $map[$fieldKey] = CatalogHelperContent::htmlForQuery(new CatalogHelperQuery(
                screenCode: $screenCode,
                code: self::helperCode($fieldKey),
                serviceTypeId: $serviceTypeId,
                accountTypeId: $accountTypeId,
            ));
        }

        return $map;
    }

    /**
     * Helper for price list item pricing mode ({@code code}: {@code pricing_mode}).
     */
    public static function pricingModeHelpHtml(?int $serviceTypeId = null, ?int $accountTypeId = null): ?string
    {
        return CatalogHelperContent::htmlForQuery(new CatalogHelperQuery(
            screenCode: ServiceWizardHelperScreens::STEP4_VARIANTS,
            code: 'pricing_mode',
            serviceTypeId: $serviceTypeId,
            accountTypeId: $accountTypeId,
        ));
    }
}
