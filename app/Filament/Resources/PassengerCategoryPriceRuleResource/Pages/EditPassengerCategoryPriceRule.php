<?php

namespace App\Filament\Resources\PassengerCategoryPriceRuleResource\Pages;

use App\Filament\Resources\PassengerCategoryPriceRuleResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPassengerCategoryPriceRule extends LmpEditRecord
{
    protected static string $resource = PassengerCategoryPriceRuleResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
