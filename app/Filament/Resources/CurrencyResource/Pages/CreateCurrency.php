<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCurrency extends LmpCreateRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
