<?php

namespace App\Filament\Resources\CatFaqResource\Pages;

use App\Filament\Resources\CatFaqResource;
use App\Models\CatFaq;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditCatFaq extends LmpEditRecord
{
    protected static string $resource = CatFaqResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var CatFaq $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];

        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = [
                'question' => $trans?->question ?? '',
                'answer' => $trans?->answer ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = CatFaqResource::normalizeScopeFields($data);
        CatFaqResource::assertUniqueCodeAccountType($data, $this->getRecord()->getKey());

        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        /** @var CatFaq $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatFaqResource::syncTranslationsFromForm($record, $translations, false);
    }
}
