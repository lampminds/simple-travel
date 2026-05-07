<?php

/**
 * Catalog UI: service / variant status badges and Feather icons.
 *
 * - badge: Bootstrap 5 color name used as suffix for `badge text-bg-{badge}`.
 * - icon: Feather icon name, or null to hide the icon.
 *
 * Customize via `service_status_overrides` / `service_variant_status_overrides` (same shape as presets).
 */
return [

    'service_status_presets' => [
        'active' => ['badge' => 'success', 'icon' => 'check-circle'],
        'onhold' => ['badge' => 'warning', 'icon' => 'clock'],
        'suspended' => ['badge' => 'danger', 'icon' => 'alert-circle'],
        'discontinued' => ['badge' => 'secondary', 'icon' => 'archive'],
        'inactive' => ['badge' => 'secondary', 'icon' => 'pause-circle'],
        'terminated' => ['badge' => 'dark', 'icon' => 'slash'],
    ],

    /**
     * Merged on top of service_status_presets (same shape). Handy for env-specific tweaks.
     */
    'service_status_overrides' => [],

    'service_variant_status_presets' => [
        'active' => ['badge' => 'success', 'icon' => 'check-circle'],
        'inactive' => ['badge' => 'secondary', 'icon' => 'minus-circle'],
        'hidden' => ['badge' => 'info', 'icon' => 'eye-off'],
        'suspended' => ['badge' => 'warning', 'icon' => 'alert-triangle'],
        'discontinued' => ['badge' => 'secondary', 'icon' => 'archive'],
    ],

    'service_variant_status_overrides' => [],

];
