<?php

namespace App\Filament\Resources\OperatorPriceListResource\Pages;

use App\Filament\Resources\OperatorPriceListResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateOperatorPriceList extends LmpCreateRecord
{
    protected static string $resource = OperatorPriceListResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
