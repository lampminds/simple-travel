<?php

namespace App\Filament\Resources\CatHelperResource\Pages;

use App\Filament\Resources\CatHelperResource;
use App\Models\CatHelper;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditCatHelper extends LmpEditRecord
{
    protected static string $resource = CatHelperResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var CatHelper $record */
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];

        foreach (Language::query()->with('locale')->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = [
                'text' => $trans?->text ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = CatHelperResource::normalizeScopeFields($data);
        CatHelperResource::assertUniqueScope($data, $this->getRecord()->getKey());

        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        /** @var CatHelper $record */
        $record = $this->getRecord();
        $translations = $this->form->getState()['translations'] ?? [];

        CatHelperResource::syncTranslationsFromForm($record, $translations, false);
    }
}
