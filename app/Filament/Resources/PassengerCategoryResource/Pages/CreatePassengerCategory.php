<?php

namespace App\Filament\Resources\PassengerCategoryResource\Pages;

use App\Filament\Resources\PassengerCategoryResource;
use App\Models\Language;
use App\Models\PassengerCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreatePassengerCategory extends LmpCreateRecord
{
    protected static string $resource = PassengerCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $language) {
            $translations[$language->id] = ['name' => ''];
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
        /** @var PassengerCategory $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        PassengerCategoryResource::syncTranslations($record, $translations, true);
    }
}
