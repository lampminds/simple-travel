<?php

namespace App\Filament\Resources\ServiceUserPriceResource\Pages;

use App\Models\Language;
use App\Models\ServiceUserPrice;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceUserPrice extends LmpCreateRecord
{
    protected static string $resource = \App\Filament\Resources\ServiceUserPriceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['price' => null, 'description' => null];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        $this->syncTranslations($this->getRecord(), $this->form->getState()['translations'] ?? []);
    }

    protected function syncTranslations(ServiceUserPrice $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            $description = $row['description'] ?? null;
            $price = isset($row['price']) && $row['price'] !== '' && $row['price'] !== null ? $row['price'] : null;
            if ($description !== null || $price !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'price' => $price,
                    'description' => $description,
                ]);
            }
        }
    }
}
