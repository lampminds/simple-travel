<?php

namespace App\Filament\Resources\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransferResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceTransfer extends LmpCreateRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
