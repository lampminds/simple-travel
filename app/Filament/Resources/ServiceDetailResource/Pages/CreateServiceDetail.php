<?php

namespace App\Filament\Resources\ServiceDetailResource\Pages;

use App\Filament\Resources\ServiceDetailResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceDetail extends LmpCreateRecord
{
    protected static string $resource = ServiceDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
