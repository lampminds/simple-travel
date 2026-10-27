<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\AccountPublicProfile;
use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditAccount extends LmpEditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);
        $record = $this->getRecord();

        if (! $record instanceof Account) {
            return $data;
        }

        return AccountResource::mergePublicProfileIntoFormData(
            $record,
            $record->mergePrimaryAddressIntoFormData($data),
        );
    }

    /**
     * Persist BelongsToMany / HasMany form tabs (tax IDs, type categories) after the main account row update.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $address = Account::pullAddressInput($data);
        $profileInput = null;
        if (array_key_exists('public_profile_video_url', $data) || array_key_exists('public_profile_business_hours', $data) || array_key_exists('public_profile_translations', $data)) {
            $profileInput = AccountResource::pullPublicProfileInput($data);
        }

        $record = parent::handleRecordUpdate($record, $data);
        $this->form->model($record)->saveRelationships();

        if ($record instanceof Account) {
            $record->syncPrimaryAddress($address);
            if (is_array($profileInput)) {
                AccountPublicProfile::syncForAccount($record, $profileInput);
            }
        }

        return $record;
    }
}
