<?php

namespace App\Filament\Resources\TodoCategoryResource\Pages;

use App\Filament\Resources\TodoCategoryResource;
use App\Models\Language;
use App\Models\TodoCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditTodoCategory extends LmpEditRecord
{
    protected static string $resource = TodoCategoryResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name,
                'description' => $trans->description ?? '',
            ] : ['name' => '', 'description' => ''];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        $this->syncTranslations($this->getRecord(), $this->form->getState()['translations'] ?? []);
    }

    /**
     * @param  array<int|string, array{name?: string, description?: string}>  $translations
     */
    protected function syncTranslations(TodoCategory $record, array $translations): void
    {
        $record->translations()->delete();
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
