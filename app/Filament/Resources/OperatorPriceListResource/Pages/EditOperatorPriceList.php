<?php

namespace App\Filament\Resources\OperatorPriceListResource\Pages;

use App\Filament\Resources\OperatorPriceListResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditOperatorPriceList extends LmpEditRecord
{
    protected static string $resource = OperatorPriceListResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
