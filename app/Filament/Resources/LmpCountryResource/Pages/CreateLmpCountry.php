<?php

namespace App\Filament\Resources\LmpCountryResource\Pages;

use App\Filament\Resources\LmpCountryResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateLmpCountry extends LmpCreateRecord
{
    protected static string $resource = LmpCountryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
