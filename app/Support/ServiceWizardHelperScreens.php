<?php

namespace App\Support;

/**
 * {@code screen_code} values for service wizard catalog helpers (see cat_helpers.screen_code).
 */
final class ServiceWizardHelperScreens
{
    public const STEP1_SERVICE_DESCRIPTION = 'service_wizard_step1';

    public const STEP2 = 'service_wizard_step2';

    public const STEP4_VARIANTS = 'service_wizard_step4_variants';

    public const STEP6_CONDITIONS = 'service_wizard_step6';

    /** @deprecated Use {@see STEP4_VARIANTS} */
    public const STEP4_VARIANT_DESCRIPTION = self::STEP4_VARIANTS;
}
