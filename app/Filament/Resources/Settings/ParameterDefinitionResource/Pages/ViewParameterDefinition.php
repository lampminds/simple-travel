<?php

namespace App\Filament\Resources\Settings\ParameterDefinitionResource\Pages;

use App\Filament\Resources\Settings\ParameterDefinitionResource;
use App\Models\Language;
use App\Models\ParameterValue;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewParameterDefinition extends LmpViewRecord
{
    protected static string $resource = ParameterDefinitionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load([
            'translations',
            'parameterOptions.translations',
            'parameterValues',
        ]);

        $languages = Language::query()->orderBy('id')->get();

        $data['translations'] = [];
        foreach ($languages as $lang) {
            $trans = $record->translations->firstWhere('language_id', $lang->id);
            $data['translations'][$lang->id] = $trans ? [
                'name' => $trans->name,
                'description' => $trans->description,
                'help' => $trans->help,
            ] : ['name' => '', 'description' => '', 'help' => null];
        }

        $data['options'] = $record->parameterOptions
            ->sortBy('sort_order')
            ->values()
            ->map(function ($opt) use ($languages) {
                $translations = [];
                foreach ($languages as $lang) {
                    $t = $opt->translations->firstWhere('language_id', $lang->id);
                    $translations[$lang->id] = ['name' => $t?->name ?? ''];
                }

                return [
                    'value' => $opt->value,
                    'sort_order' => $opt->sort_order,
                    'translations' => $translations,
                ];
            })
            ->all();

        $useSelect = ParameterDefinitionResource::definitionUsesStoredOptions($record);

        $data['parameter_values'] = $record->parameterValues->map(function (ParameterValue $pv) use ($useSelect) {
            return [
                'account_id' => $pv->account_id,
                'value_select' => $useSelect ? $pv->value : null,
                'value_free' => ! $useSelect ? $pv->value : null,
            ];
        })->all();

        return $data;
    }
}
