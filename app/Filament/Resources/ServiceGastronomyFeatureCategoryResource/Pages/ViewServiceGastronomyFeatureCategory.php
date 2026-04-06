<?php

namespace App\Filament\Resources\ServiceGastronomyFeatureCategoryResource\Pages;

use App\Filament\Resources\ServiceGastronomyFeatureCategoryResource;
use App\Models\Language;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewServiceGastronomyFeatureCategory extends LmpViewRecord
{
    protected static string $resource = ServiceGastronomyFeatureCategoryResource::class;

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
}
