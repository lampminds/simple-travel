<?php

namespace App\Filament\Resources\PassengerCategoryResource\Pages;

use App\Filament\Resources\PassengerCategoryResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreatePassengerCategory extends LmpCreateRecord
{
    protected static string $resource = PassengerCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
