<?php

namespace App\Filament\Resources\ProviderPassengerPolicyResource\Pages;

use App\Filament\Resources\ProviderPassengerPolicyResource;
use App\Models\PassengerCategory;
use App\Models\ProviderPassengerPolicy;
use App\Services\PassengerPolicyWriter;
use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditProviderPassengerPolicy extends LmpEditRecord
{
    protected static string $resource = ProviderPassengerPolicyResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var ProviderPassengerPolicy $record */
        $record = $this->getRecord();
        $record->load('rules');

        $data['rules'] = [];

        foreach (PassengerCategory::query()->ordered()->get() as $category) {
            $band = $record->rules->firstWhere('passenger_category_id', $category->id);
            $data['rules'][$category->id] = [
                'included' => $band !== null,
                'age_min' => $band->age_min ?? 0,
                'age_max' => $band->age_max ?? null,
            ];
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $rules = is_array($data['rules'] ?? null) ? $data['rules'] : [];
        CreateProviderPassengerPolicy::assertHasBand($rules);

        /** @var ProviderPassengerPolicy $record */
        app(PassengerPolicyWriter::class)->syncPolicy(
            $record,
            (int) $record->provider_id,
            (string) ($data['name'] ?? $record->name),
            $rules,
        );

        return $record->refresh();
    }
}
