<?php

namespace App\Filament\Resources\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransferResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceTransfer extends LmpEditRecord
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
