<?php

namespace App\Filament\Resources\Settings;

use BackedEnum;
use Lampminds\Customization\Resources\ParameterResource as BaseParameterResource;

class ParameterResource extends BaseParameterResource
{
    /**
     * Place the Parameters resource under the common Settings / Configuración
     * navigation group used by other configuration resources.
     */
    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    public static function getNavigationGroup(): string
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum ? $group->value : (string) __($group);
    }
}

