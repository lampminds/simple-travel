<?php

namespace App\Filament\Resources\ServiceTransferPriceResource\Pages;

use App\Filament\Resources\ServiceTransferPriceResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceTransferPrice extends LmpEditRecord
{
    protected static string $resource = ServiceTransferPriceResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
