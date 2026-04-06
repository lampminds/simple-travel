<?php

namespace App\Filament\Resources\ServiceFeatureCategoryResource\Pages;

use App\Filament\Resources\ServiceFeatureCategoryResource;
use App\Models\Language;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewServiceFeatureCategory extends LmpViewRecord
{
    protected static string $resource = ServiceFeatureCategoryResource::class;

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

