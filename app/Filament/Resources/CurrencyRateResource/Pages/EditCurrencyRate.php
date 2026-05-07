<?php

namespace App\Filament\Resources\CurrencyRateResource\Pages;

use App\Filament\Resources\CurrencyRateResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditCurrencyRate extends LmpEditRecord
{
    protected static string $resource = CurrencyRateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);
        $data = CurrencyRateResource::normalizeStartingAtToDayStart($data);
        $data = CurrencyRateResource::normalizeUsdRate($data);
        if (isset($data['currency_id'], $data['starting_at'])) {
            CurrencyRateResource::assertUniqueStartingAt((int) $data['currency_id'], $data['starting_at'], (int) $this->record->getKey());
        }

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
