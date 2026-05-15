<?php

namespace App\Filament\Resources\ServiceTransferVehicleResource\Pages;

use App\Filament\Resources\ServiceTransferVehicleResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceTransferVehicle extends LmpCreateRecord
{
    protected static string $resource = ServiceTransferVehicleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
