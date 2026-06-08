<?php

namespace App\Filament\Resources\CatGenderResource\Pages;

use App\Filament\Resources\CatGenderResource;
use App\Models\CatGender;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCatGender extends LmpCreateRecord
{
    protected static string $resource = CatGenderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => ''];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        CatGenderResource::assertUniqueCode($data, null);

        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        /** @var CatGender $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatGenderResource::syncTranslationsFromForm($record, $translations, true);
    }
}
