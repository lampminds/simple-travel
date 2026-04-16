<?php

namespace App\Filament\Resources\TodoCategoryResource\Pages;

use App\Filament\Resources\TodoCategoryResource;
use App\Models\Language;
use App\Models\TodoCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateTodoCategory extends LmpCreateRecord
{
    protected static string $resource = TodoCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = ['name' => '', 'description' => ''];
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

    /**
     * @param  array<int|string, array{name?: string, description?: string}>  $translations
     */
    protected function syncTranslations(TodoCategory $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '') && empty(trim((string) ($row['description'] ?? '')))) {
                continue;
            }
            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
                'description' => ($row['description'] ?? '') !== '' ? $row['description'] : null,
            ]);
        }
    }
}
