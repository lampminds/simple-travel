<?php

namespace App\Filament\Resources\ProviderPriceListResource\Pages;

use App\Filament\Resources\ProviderPriceListResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditProviderPriceList extends LmpEditRecord
{
    protected static string $resource = ProviderPriceListResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
