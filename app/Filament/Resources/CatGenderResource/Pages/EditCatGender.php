<?php

namespace App\Filament\Resources\CatGenderResource\Pages;

use App\Filament\Resources\CatGenderResource;
use App\Models\CatGender;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditCatGender extends LmpEditRecord
{
    protected static string $resource = CatGenderResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var CatGender $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];

        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = [
                'name' => $trans?->name ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        CatGenderResource::assertUniqueCode($data, $this->getRecord()->getKey());

        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        /** @var CatGender $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatGenderResource::syncTranslationsFromForm($record, $translations, false);
    }
}
