<?php

namespace App\Filament\Resources\ServiceDetailTopicResource\Pages;

use App\Filament\Resources\ServiceDetailTopicResource;
use App\Models\Language;
use App\Models\ServiceDetailTopic;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceDetailTopic extends LmpEditRecord
{
    protected static string $resource = ServiceDetailTopicResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        $languages = Language::query()->orderBy('id')->get();
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name ?? '',
                'description' => $trans->description,
            ] : ['name' => '', 'description' => null];
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

    protected function syncTranslations(ServiceDetailTopic $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            $name = $row['name'] ?? '';
            $description = $row['description'] ?? null;
            if ($name !== '' || $description !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }
    }
}
