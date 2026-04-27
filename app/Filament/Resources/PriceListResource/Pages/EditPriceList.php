<?php

namespace App\Filament\Resources\PriceListResource\Pages;

use App\Filament\Resources\PriceListResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPriceList extends LmpEditRecord
{
    protected static string $resource = PriceListResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
