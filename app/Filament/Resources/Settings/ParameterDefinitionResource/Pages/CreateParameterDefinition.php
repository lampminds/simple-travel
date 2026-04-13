<?php

namespace App\Filament\Resources\Settings\ParameterDefinitionResource\Pages;

use App\Filament\Resources\Settings\ParameterDefinitionResource;
use App\Models\Language;
use App\Models\ParameterDefinition;
use App\Models\ParameterValue;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateParameterDefinition extends LmpCreateRecord
{
    protected static string $resource = ParameterDefinitionResource::class;

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
                'help' => null,
            ];
        }
        $state = $this->form->getRawState() ?? [];
        $state['translations'] = $translations;
        $state['options'] = [];
        $state['parameter_values'] = [];
        $this->form->fill($state);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->assertParameterOptionsFulfillUi($data);

        return Arr::except($data, ['translations', 'options', 'parameter_values']);
    }

    protected function afterCreate(): void
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
