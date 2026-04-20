<?php

namespace App\Filament\Resources\TodoTaskResource\Pages;

use App\Filament\Resources\TodoTaskResource;
use App\Models\Language;
use App\Models\TodoTask;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateTodoTask extends LmpCreateRecord
{
    protected static string $resource = TodoTaskResource::class;

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
        $data = Arr::except($data, ['translations']);

        if (($data['action_type'] ?? TodoTask::ACTION_NONE) === TodoTask::ACTION_NONE) {
            $data['action_url'] = null;
        }

        if (($data['verification_type'] ?? TodoTask::VERIFICATION_NONE) === TodoTask::VERIFICATION_NONE) {
            $data['verification_url'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $translations = $this->form->getState()['translations'] ?? [];
        $this->syncTranslations($this->getRecord(), $translations);
    }

    /**
     * @param  array<int|string, array{name?: string, description?: string}>  $translations
     */
    protected function syncTranslations(TodoTask $record, array $translations): void
    {
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
