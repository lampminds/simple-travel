<?php

namespace App\Filament\Resources\AccountCategoryResource\Pages;

use App\Filament\Resources\AccountCategoryResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewAccountCategory extends LmpViewRecord
{
    protected static string $resource = AccountCategoryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load('translations');
        $data['translations'] = [];
        $languages = \App\Models\Language::query()->orderBy('id')->get();
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name,
                'description' => $trans->description,
            ] : ['name' => '', 'description' => null];
        }

        return $data;
    }
}
