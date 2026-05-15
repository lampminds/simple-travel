<?php

namespace App\Filament\Resources\ProviderPriceListItemResource\Pages;

use App\Filament\Resources\ProviderPriceListItemResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditProviderPriceListItem extends LmpEditRecord
{
    protected static string $resource = ProviderPriceListItemResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
