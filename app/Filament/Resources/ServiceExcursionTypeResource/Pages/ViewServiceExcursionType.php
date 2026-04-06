<?php

namespace App\Filament\Resources\ServiceExcursionTypeResource\Pages;

use App\Filament\Resources\ServiceExcursionTypeResource;
use App\Models\Language;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewServiceExcursionType extends LmpViewRecord
{
    protected static string $resource = ServiceExcursionTypeResource::class;

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
