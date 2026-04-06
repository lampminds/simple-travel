<?php

namespace App\Filament\Resources\Settings\ParameterValueResource\Pages;

use App\Filament\Resources\Settings\ParameterValueResource;
use App\Models\ParameterDefinition;
use App\Models\ParameterValue;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateParameterValue extends LmpCreateRecord
{
    protected static string $resource = ParameterValueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        $defId = (int) ($data['parameter_definition_id'] ?? 0);
        $def = ParameterDefinition::find($defId);
        if ($def && $def->scope === 'system') {
            $data['account_id'] = null;
        }

        $accountId = isset($data['account_id']) && $data['account_id'] !== '' && $data['account_id'] !== null
            ? (int) $data['account_id']
            : null;

        ParameterValue::assertUniqueCombination($defId, $accountId);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
