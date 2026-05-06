<?php

namespace App\Filament\Resources\LmpCityResource\Pages;

use App\Filament\Resources\LmpCityResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateLmpCity extends LmpCreateRecord
{
    protected static string $resource = LmpCityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
