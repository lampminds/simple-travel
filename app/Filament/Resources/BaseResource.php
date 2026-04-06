<?php

namespace App\Filament\Resources;

use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

/**
 * Base resource that adds a navigation badge with the total record count.
 */
abstract class BaseResource extends LmpResource
{
    public static function getNavigationBadge(): ?string
    {
        $model = static::getModel();

        if (! method_exists($model, 'count')) {
            return null;
        }

        return (string) $model::count();
    }

    /**
     * Return badge as a Closure so it is evaluated when the sidebar renders
     * (each resource class gets the correct count).
     */
    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();
        $resourceClass = static::class;

        foreach ($items as $item) {
            $item->badge(
                fn (): ?string => $resourceClass::getNavigationBadge(),
                color: static::getNavigationBadgeColor(),
            );
            $item->badgeTooltip(static::getNavigationBadgeTooltip());
        }

        return $items;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('filament.common.navigation_badge_tooltip');
    }
}
