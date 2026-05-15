<?php

namespace App\Filament\Resources\ServiceTransferVehicleTypeResource\Pages;

use App\Filament\Resources\ServiceTransferVehicleTypeResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceTransferVehicleType extends LmpEditRecord
{
    protected static string $resource = ServiceTransferVehicleTypeResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
