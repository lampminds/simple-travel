<?php

namespace App\Filament\Resources\LmpCountryResource\Pages;

use App\Filament\Resources\LmpCountryResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditLmpCountry extends LmpEditRecord
{
    protected static string $resource = LmpCountryResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
