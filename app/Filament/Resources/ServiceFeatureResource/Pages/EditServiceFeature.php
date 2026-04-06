<?php

namespace App\Filament\Resources\ServiceFeatureResource\Pages;

use App\Filament\Resources\ServiceFeatureResource;
use App\Models\Language;
use App\Models\ServiceFeature;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceFeature extends LmpEditRecord
{
    protected static string $resource = ServiceFeatureResource::class;

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
                'description' => $trans->description ?? '',
            ] : [
                'name' => '',
                'description' => '',
            ];
        }

        $data['scopes'] = $record->is_selectable
            ? DB::table('cat_service_feature_scopes')
                ->where('service_feature_id', $record->id)
                ->pluck('service_type_id')
                ->map(fn ($id) => (string) $id)
                ->unique()
                ->values()
                ->all()
            : [];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['translations', 'scopes']);
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        $this->syncTranslations($this->getRecord(), $state['translations'] ?? []);
        $this->syncScopes($this->getRecord(), $state['scopes'] ?? []);
    }

    /**
     * @param array<int|string, array{name:string, description:string}> $translations
     */
    protected function syncTranslations(ServiceFeature $record, array $translations): void
    {
        $record->translations()->delete();

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
        DB::table('cat_service_feature_scopes')
            ->where('service_feature_id', $record->id)
            ->delete();

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

