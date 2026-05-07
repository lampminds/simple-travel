<?php

namespace App\Filament\Resources\CurrencyRateResource\Pages;

use App\Filament\Resources\CurrencyRateResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCurrencyRate extends LmpCreateRecord
{
    protected static string $resource = CurrencyRateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $data = CurrencyRateResource::normalizeStartingAtToDayStart($data);
        $data = CurrencyRateResource::normalizeUsdRate($data);
        if (isset($data['currency_id'], $data['starting_at'])) {
            CurrencyRateResource::assertUniqueStartingAt((int) $data['currency_id'], $data['starting_at']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
