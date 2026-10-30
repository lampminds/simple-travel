<?php

namespace App\Filament\Resources\PassengerCategoryResource\Pages;

use App\Filament\Resources\PassengerCategoryResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPassengerCategory extends LmpEditRecord
{
    protected static string $resource = PassengerCategoryResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
