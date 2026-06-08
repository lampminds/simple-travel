<?php

namespace App\Filament\Resources\CatFaqResource\Pages;

use App\Filament\Resources\CatFaqResource;
use App\Models\CatFaq;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCatFaq extends LmpCreateRecord
{
    protected static string $resource = CatFaqResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['question' => '', 'answer' => ''];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = CatFaqResource::normalizeScopeFields($data);
        CatFaqResource::assertUniqueCodeAccountType($data, null);

        return Arr::except($data, ['translations']);
    }

    protected function afterCreate(): void
    {
        /** @var CatFaq $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatFaqResource::syncTranslationsFromForm($record, $translations, true);
    }
}
