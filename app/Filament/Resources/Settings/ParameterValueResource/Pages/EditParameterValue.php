<?php

namespace App\Filament\Resources\Settings\ParameterValueResource\Pages;

use App\Filament\Resources\Settings\ParameterValueResource;
use App\Models\ParameterDefinition;
use App\Models\ParameterValue;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditParameterValue extends LmpEditRecord
{
    protected static string $resource = ParameterValueResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        $record = $this->getRecord();
        $defId = (int) ($data['parameter_definition_id'] ?? $record->parameter_definition_id);

        if (ParameterDefinition::queryUsesOptionBackedValue($defId)) {
            $data['value_select'] = $record->value;
            $data['value_free'] = '';
        } else {
            $data['value_free'] = $record->value;
            $data['value_select'] = null;
        }

        unset($data['value']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['value'] = $data['value_select'] ?? $data['value_free'] ?? null;
        unset($data['value_select'], $data['value_free']);

        $data = parent::mutateFormDataBeforeSave($data);

        $defId = (int) ($data['parameter_definition_id'] ?? $this->record->parameter_definition_id);
        $def = ParameterDefinition::find($defId);
        if ($def && $def->scope === 'system') {
            $data['account_id'] = null;
        }

        $accountId = isset($data['account_id']) && $data['account_id'] !== '' && $data['account_id'] !== null
            ? (int) $data['account_id']
            : null;

        ParameterValue::assertUniqueCombination($defId, $accountId, $this->record->id);

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
