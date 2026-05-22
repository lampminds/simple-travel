<?php

namespace App\Filament\Resources\ServiceDetailConditionKeyResource\Pages;

use App\Filament\Resources\ServiceDetailConditionKeyResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceDetailConditionKey extends LmpEditRecord
{
    protected static string $resource = ServiceDetailConditionKeyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
