<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Enums\PriceAdjustmentType;
use App\Filament\Resources\ServiceResource;
use App\Models\Language;
use App\Models\Service;
use App\Services\PassengerPolicyWriter;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditService extends LmpEditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        $languages = Language::query()->orderBy('id')->get();
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name,
                'description' => $trans->description,
            ] : ['name' => '', 'description' => null];
        }

        $data['variant_prices'] = [];
        if ($record instanceof Service) {
            $record->load('serviceVariants.passengerPriceRules.policyRule');
            foreach ($record->serviceVariants as $variant) {
                foreach ($variant->passengerPriceRules as $price) {
                    $categoryId = $price->policyRule?->passenger_category_id;
                    if ($categoryId === null) {
                        continue;
                    }

                    $data['variant_prices'][] = [
                        'service_variant_id' => $variant->id,
                        'passenger_category_id' => $categoryId,
                        'adjustment_type' => $price->adjustment_type instanceof PriceAdjustmentType
                            ? $price->adjustment_type->value
                            : (string) $price->adjustment_type,
                        'adjustment_value' => $price->adjustment_value,
                    ];
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations', 'variant_prices']);
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        $record = $this->getRecord();
        $this->syncTranslations($record, $state['translations'] ?? []);

        if ($record instanceof Service) {
            $prices = [];
            foreach (is_array($state['variant_prices'] ?? null) ? $state['variant_prices'] : [] as $key => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $row['error_key'] = 'variant_prices.'.$key;
                $prices[] = $row;
            }

            app(PassengerPolicyWriter::class)->syncVariantPrices($record, $prices);
        }
    }

    protected function syncTranslations(Service $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '') && empty($row['description'] ?? '')) {
                continue;
            }
            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? null,
            ]);
        }
    }
}
