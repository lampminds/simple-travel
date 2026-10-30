<?php

namespace App\Filament\Resources\PassengerCategoryPriceRuleResource\Pages;

use App\Filament\Resources\PassengerCategoryPriceRuleResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreatePassengerCategoryPriceRule extends LmpCreateRecord
{
    protected static string $resource = PassengerCategoryPriceRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
