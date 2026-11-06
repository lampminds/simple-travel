<?php

namespace App\Filament\Resources\ProviderPassengerPolicyResource\Pages;

use App\Filament\Resources\ProviderPassengerPolicyResource;
use App\Models\ProviderPassengerPolicy;
use App\Services\PassengerPolicyWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateProviderPassengerPolicy extends LmpCreateRecord
{
    protected static string $resource = ProviderPassengerPolicyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $rules = is_array($data['rules'] ?? null) ? $data['rules'] : [];
        static::assertHasBand($rules);

        return app(PassengerPolicyWriter::class)->syncPolicy(
            null,
            (int) $data['provider_id'],
            (string) ($data['name'] ?? ''),
            $rules,
        );
    }

    /**
     * @param  array<int|string, mixed>  $rules
     */
    public static function assertHasBand(array $rules): void
    {
        foreach ($rules as $row) {
            if (is_array($row) && filter_var($row['included'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                return;
            }
        }

        $first = array_key_first($rules);
        $key = $first === null ? 'provider_id' : 'rules.'.$first.'.included';

        throw ValidationException::withMessages([
            $key => (string) __('account.passenger_policies.validation.bands_required'),
        ]);
    }
}
