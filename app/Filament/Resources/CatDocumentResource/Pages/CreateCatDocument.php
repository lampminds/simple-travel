<?php

namespace App\Filament\Resources\CatDocumentResource\Pages;

use App\Filament\Resources\CatDocumentResource;
use App\Models\CatDocument;
use App\Models\Language;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateCatDocument extends LmpCreateRecord
{
    protected static string $resource = CatDocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();
        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => '', 'description' => null];
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
        $translations = $this->form->getState()['translations'] ?? [];
        $this->syncTranslations($this->getRecord(), $translations);
    }

    protected function syncTranslations(CatDocument $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            if (empty($row['name']) && empty($row['description'] ?? '')) {
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
