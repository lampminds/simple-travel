<?php

namespace App\Filament\Resources\Settings\ParameterDefinitionResource\Pages;

use App\Filament\Resources\Settings\ParameterDefinitionResource;
use App\Models\Language;
use App\Models\ParameterDefinition;
use App\Models\ParameterValue;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditParameterDefinition extends LmpEditRecord
{
    protected static string $resource = ParameterDefinitionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->assertParameterOptionsFulfillUi($data);

        return Arr::except($data, ['translations', 'options', 'parameter_values']);
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        $record = $this->getRecord();
        $this->syncTranslations($record, $state['translations'] ?? []);
        $this->syncOptions($record, $state['options'] ?? []);
        $this->syncParameterValues($record, $state);
    }

    protected function assertParameterOptionsFulfillUi(array $data): void
    {
        $ui = $data['ui_component'] ?? 'input';
        if (! ParameterDefinition::uiComponentRequiresOptions($ui)) {
            return;
        }

        $filled = array_values(array_filter(
            $data['options'] ?? [],
            static fn (array $row): bool => ($row['value'] ?? '') !== ''
        ));

        if (count($filled) < 2) {
            throw ValidationException::withMessages([
                'options' => __('filament.resources.parameter_definition_options_min_two'),
            ]);
        }
    }

    protected function syncTranslations(ParameterDefinition $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            if (
                empty($row['name'] ?? '')
                && empty($row['description'] ?? '')
                && empty($row['help'] ?? '')
            ) {
                continue;
            }
            $record->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? null,
                'help' => $row['help'] ?? null,
            ]);
        }
    }

    protected function syncOptions(ParameterDefinition $record, array $options): void
    {
        foreach ($record->parameterOptions as $opt) {
            $opt->translations()->delete();
        }
        $record->parameterOptions()->delete();

        foreach ($options as $row) {
            if (($row['value'] ?? '') === '') {
                continue;
            }
            $option = $record->parameterOptions()->create([
                'value' => $row['value'],
                'sort_order' => (int) ($row['sort_order'] ?? 0),
            ]);
            foreach ($row['translations'] ?? [] as $languageId => $t) {
                if (empty($t['name'] ?? '')) {
                    continue;
                }
                $option->translations()->create([
                    'language_id' => $languageId,
                    'name' => $t['name'],
                ]);
            }
        }
    }

    protected function syncParameterValues(ParameterDefinition $record, array $formState): void
    {
        $record->parameterValues()->delete();

        $rows = $formState['parameter_values'] ?? [];
        $ui = $record->ui_component;
        $optionsState = $formState['options'] ?? [];
        $seenTenantKeys = [];

        foreach ($rows as $row) {
            $accountId = isset($row['account_id']) && $row['account_id'] !== '' && $row['account_id'] !== null
                ? (int) $row['account_id']
                : null;

            if ($record->scope === 'system') {
                $accountId = null;
            }

            if ($record->scope === 'tenant') {
                $dedupeKey = $accountId === null ? '__global__' : (string) $accountId;
                if (isset($seenTenantKeys[$dedupeKey])) {
                    throw ValidationException::withMessages([
                        'parameter_values' => __('filament.resources.parameter_definition_values_duplicate_account'),
                    ]);
                }
                $seenTenantKeys[$dedupeKey] = true;
            }

            $value = ParameterDefinitionResource::resolvedValueFromRepeaterRow($row, $ui, $optionsState);
            if ($value === null || $value === '') {
                continue;
            }

            ParameterValue::assertUniqueCombination($record->id, $accountId);

            $record->parameterValues()->create([
                'parameter_definition_id' => $record->id,
                'account_id' => $accountId,
                'value' => $value,
            ]);
        }
    }
}
