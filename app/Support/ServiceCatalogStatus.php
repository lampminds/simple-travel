<?php

namespace App\Support;

/**
 * Resolves badge + icon + label for catalog tables. Defaults live in config/service_catalog.php.
 */
final class ServiceCatalogStatus
{
    /** @var list<string> */
    private const ALLOWED_BADGES = [
        'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark',
    ];

    /**
     * @return array{badge: string, icon: string|null, label: string}
     */
    public static function forService(?string $status): array
    {
        $presets = array_replace_recursive(
            config('service_catalog.service_status_presets', []),
            config('service_catalog.service_status_overrides', []),
        );

        return self::build($status, $presets, 'filament.resources.service_status.');
    }

    /**
     * @return array{badge: string, icon: string|null, label: string}
     */
    public static function forVariant(?string $status): array
    {
        $presets = array_replace_recursive(
            config('service_catalog.service_variant_status_presets', []),
            config('service_catalog.service_variant_status_overrides', []),
        );

        return self::build($status, $presets, 'filament.resources.service_variant_status.');
    }

    /**
     * @param  array<string, array{badge?: string, icon?: string|null}>  $presets
     * @return array{badge: string, icon: string|null, label: string}
     */
    private static function build(?string $status, array $presets, string $translationKeyPrefix): array
    {
        if ($status === null || $status === '') {
            return [
                'badge' => 'light',
                'icon' => null,
                'label' => '—',
            ];
        }

        $preset = $presets[$status] ?? ['badge' => 'secondary', 'icon' => 'help-circle'];
        $badge = (string) ($preset['badge'] ?? 'secondary');
        if (! in_array($badge, self::ALLOWED_BADGES, true)) {
            $badge = 'secondary';
        }
        $icon = $preset['icon'] ?? null;
        if ($icon !== null && $icon !== '') {
            $icon = (string) $icon;
        } else {
            $icon = null;
        }

        $label = __($translationKeyPrefix.$status);
        if ($label === $translationKeyPrefix.$status) {
            $label = $status;
        }

        return [
            'badge' => $badge,
            'icon' => $icon,
            'label' => $label,
        ];
    }
}
