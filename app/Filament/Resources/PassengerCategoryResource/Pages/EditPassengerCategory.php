<?php

namespace App\Filament\Resources\PassengerCategoryResource\Pages;

use App\Filament\Resources\PassengerCategoryResource;
use App\Models\Language;
use App\Models\PassengerCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditPassengerCategory extends LmpEditRecord
{
    protected static string $resource = PassengerCategoryResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var PassengerCategory $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];

        foreach (Language::query()->orderBy('id')->get() as $language) {
            $translation = $record->translations->firstWhere('language_id', $language->id);
            $data['translations'][$language->id] = [
                'name' => $translation?->name ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        /** @var PassengerCategory $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        PassengerCategoryResource::syncTranslations($record, $translations, false);
    }
}
