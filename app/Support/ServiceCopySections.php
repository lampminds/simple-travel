<?php

namespace App\Support;

/**
 * Wizard-aligned sections selectable when duplicating a service.
 */
final class ServiceCopySections
{
    public const BASE = 'base';

    public const STATUS = 'status';

    public const FEATURES = 'features';

    public const VARIANTS = 'variants';

    public const IMAGES = 'images';

    public const DETAILS = 'details';

    public const EXPERIENCES = 'experiences';

    public const ADVANCED = 'advanced';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::BASE,
            self::STATUS,
            self::FEATURES,
            self::VARIANTS,
            self::IMAGES,
            self::DETAILS,
            self::EXPERIENCES,
            self::ADVANCED,
        ];
    }

    /**
     * Sections available for a service type (e.g. variants skipped for some types).
     *
     * @return list<string>
     */
    public static function forServiceTypeCode(?string $code): array
    {
        $sections = self::all();

        if (ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($code)) {
            $sections = array_values(array_filter(
                $sections,
                fn (string $s): bool => $s !== self::VARIANTS
            ));
        }

        if (! ServiceWizardStepEight::isEnabledForServiceTypeCode($code)) {
            $sections = array_values(array_filter(
                $sections,
                fn (string $s): bool => $s !== self::ADVANCED
            ));
        }

        return $sections;
    }
}
