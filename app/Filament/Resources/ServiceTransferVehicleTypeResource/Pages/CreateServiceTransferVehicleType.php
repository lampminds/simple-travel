<?php

namespace App\Filament\Resources\ServiceTransferVehicleTypeResource\Pages;

use App\Filament\Resources\ServiceTransferVehicleTypeResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceTransferVehicleType extends LmpCreateRecord
{
    protected static string $resource = ServiceTransferVehicleTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['account_id'] = $data['account_id'] ?? 1;
        $data['active'] = $data['active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 9999;

        return $data;
    }
}
