<?php

namespace App\Filament\Resources\OperatorPriceListItemResource\Pages;

use App\Filament\Resources\OperatorPriceListItemResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditOperatorPriceListItem extends LmpEditRecord
{
    protected static string $resource = OperatorPriceListItemResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
