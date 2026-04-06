<?php

namespace App\Filament\Resources\ServiceFeatureResource\Pages;

use App\Filament\Resources\ServiceFeatureResource;
use App\Models\Language;
use App\Models\ServiceFeature;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceFeature extends LmpCreateRecord
{
    protected static string $resource = ServiceFeatureResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function fillForm(): void
    {
        parent::fillForm();

        $translations = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $translations[$lang->id] = [
                'name' => '',
                'description' => '',
            ];
        }

        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $state['scopes'] = [];
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Arr::except($data, ['translations', 'scopes']);
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        $translations = $state['translations'] ?? [];
        $scopes = $state['scopes'] ?? [];
        $this->syncTranslations($this->getRecord(), $translations);
        $this->syncScopes($this->getRecord(), $scopes);
    }

    /**
     * @param array<int|string, array{name:string, description:string}> $translations
     */
    protected function syncTranslations(ServiceFeature $record, array $translations): void
    {
        foreach ($translations as $languageId => $row) {
            if (empty($row['name'] ?? '')) {
                continue;
            }

            $description = trim((string) ($row['description'] ?? ''));
            if ($description === '') {
                $description = null;
            }

            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
                'description' => $description,
            ]);
        }
    }

    /**
     * @param array<int|string> $scopes Selected `cat_service_types.id` values from the checkbox list.
     */
    protected function syncScopes(ServiceFeature $record, array $scopes): void
    {
        if (! $record->is_selectable) {
            return;
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $scopes))));
        $validIds = ServiceType::query()->whereIn('id', $ids)->pluck('id')->all();

        foreach ($validIds as $serviceTypeId) {
            DB::table('cat_service_feature_scopes')->insert([
                'service_feature_id' => $record->id,
                'service_type_id' => $serviceTypeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

