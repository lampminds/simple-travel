<?php

namespace App\Filament\Resources\ServiceTransferRouteResource\Pages;

use App\Filament\Resources\ServiceTransferRouteResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceTransferRoute extends LmpCreateRecord
{
    protected static string $resource = ServiceTransferRouteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
