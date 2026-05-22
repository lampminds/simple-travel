<?php

namespace App\Filament\Resources\ServiceDetailConditionKeyResource\Pages;

use App\Filament\Resources\ServiceDetailConditionKeyResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceDetailConditionKey extends LmpCreateRecord
{
    protected static string $resource = ServiceDetailConditionKeyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
