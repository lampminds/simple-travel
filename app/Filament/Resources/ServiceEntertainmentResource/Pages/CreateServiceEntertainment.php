<?php

namespace App\Filament\Resources\ServiceEntertainmentResource\Pages;

use App\Filament\Resources\ServiceEntertainmentResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceEntertainment extends LmpCreateRecord
{
    protected static string $resource = ServiceEntertainmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}

