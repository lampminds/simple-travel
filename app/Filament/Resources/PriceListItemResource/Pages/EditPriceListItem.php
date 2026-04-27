<?php

namespace App\Filament\Resources\PriceListItemResource\Pages;

use App\Filament\Resources\PriceListItemResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPriceListItem extends LmpEditRecord
{
    protected static string $resource = PriceListItemResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
