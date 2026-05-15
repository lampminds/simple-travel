<?php

namespace App\Filament\Resources\ServiceTransferRouteResource\Pages;

use App\Filament\Resources\ServiceTransferRouteResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceTransferRoute extends LmpEditRecord
{
    protected static string $resource = ServiceTransferRouteResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
