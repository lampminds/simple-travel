<?php

namespace App\Filament\Resources\ProviderPriceListResource\Pages;

use App\Enums\PriceAdjustmentType;
use App\Filament\Resources\ProviderPriceListResource;
use App\Models\PriceList;
use App\Services\PassengerPolicyWriter;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditProviderPriceList extends LmpEditRecord
{
    protected static string $resource = ProviderPriceListResource::class;

    /**
     * @var list<array<string, mixed>>
     */
    protected array $pendingFares = [];

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var PriceList $record */
        $record = $this->getRecord();
        $record->load('items.passengerPriceRules.policyRule');

        $data['passenger_fares'] = [];

        foreach ($record->items as $item) {
            foreach ($item->passengerPriceRules as $price) {
                $data['passenger_fares'][] = [
                    'provider_price_list_item_id' => $item->id,
                    'passenger_category_id' => $price->policyRule?->passenger_category_id,
                    'adjustment_type' => $price->adjustment_type instanceof PriceAdjustmentType
                        ? $price->adjustment_type->value
                        : (string) $price->adjustment_type,
                    'adjustment_value' => $price->adjustment_value,
                ];
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $rows = is_array($data['passenger_fares'] ?? null) ? array_values($data['passenger_fares']) : [];

        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $row['error_key'] = 'passenger_fares.'.$index;
            $rows[$index] = $row;
        }

        $this->pendingFares = $rows;
        unset($data['passenger_fares']);

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var PriceList $record */
        $record = $this->getRecord();
        app(PassengerPolicyWriter::class)->syncPriceList($record, $this->pendingFares);
    }
}
