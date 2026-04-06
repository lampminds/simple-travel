<?php

namespace App\Filament\Resources\ServiceFeatureResource\Pages;

use App\Filament\Resources\ServiceFeatureResource;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewServiceFeature extends LmpViewRecord
{
    protected static string $resource = ServiceFeatureResource::class;

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

        $data['scopes'] = DB::table('cat_service_feature_scopes')
            ->where('service_feature_id', $record->id)
            ->pluck('service_type_id')
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        return $data;
    }
}

