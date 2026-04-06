<?php

namespace App\Filament\Resources\ServiceHotelTypeResource\Pages;

use App\Filament\Resources\ServiceHotelTypeResource;
use App\Models\Language;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewServiceHotelType extends LmpViewRecord
{
    protected static string $resource = ServiceHotelTypeResource::class;

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
                'description' => $trans->description,
            ] : ['name' => '', 'description' => ''];
        }

        return $data;
    }
}
