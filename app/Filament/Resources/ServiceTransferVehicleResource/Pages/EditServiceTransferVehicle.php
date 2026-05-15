<?php

namespace App\Filament\Resources\ServiceTransferVehicleResource\Pages;

use App\Filament\Resources\ServiceTransferVehicleResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceTransferVehicle extends LmpEditRecord
{
    protected static string $resource = ServiceTransferVehicleResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
