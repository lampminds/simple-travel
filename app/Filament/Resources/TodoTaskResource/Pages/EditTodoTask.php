<?php

namespace App\Filament\Resources\TodoTaskResource\Pages;

use App\Filament\Resources\TodoTaskResource;
use App\Models\Language;
use App\Models\TodoTask;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditTodoTask extends LmpEditRecord
{
    protected static string $resource = TodoTaskResource::class;

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
        $data['account_id'] = (int) config('permission.platform_account_id', 1);

        return Arr::except($data, ['translations']);
    }

    protected function afterSave(): void
    {
        $this->syncTranslations($this->getRecord(), $this->form->getState()['translations'] ?? []);
    }

    /**
     * @param  array<int|string, array{name?: string, description?: string}>  $translations
     */
    protected function syncTranslations(TodoTask $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '') && empty(trim((string) ($row['description'] ?? '')))) {
                continue;
            }
            $description = trim((string) ($row['description'] ?? ''));
            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
                'description' => $description !== '' ? $description : null,
            ]);
        }
    }
}
