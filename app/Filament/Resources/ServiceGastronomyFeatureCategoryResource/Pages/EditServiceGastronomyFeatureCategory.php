<?php

namespace App\Filament\Resources\ServiceGastronomyFeatureCategoryResource\Pages;

use App\Filament\Resources\ServiceGastronomyFeatureCategoryResource;
use App\Models\Language;
use App\Models\ServiceGastronomyFeatureCategory;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceGastronomyFeatureCategory extends LmpEditRecord
{
    protected static string $resource = ServiceGastronomyFeatureCategoryResource::class;

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
                'name' => $trans->name,
            ] : ['name' => ''];
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
     * @param array<int|string, array{name:string}> $translations
     */
    protected function syncTranslations(ServiceGastronomyFeatureCategory $record, array $translations): void
    {
        $record->translations()->delete();

        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '')) {
                continue;
            }

            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
            ]);
        }
    }
}
