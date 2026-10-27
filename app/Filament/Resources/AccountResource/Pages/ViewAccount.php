<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpViewRecord;

class ViewAccount extends LmpViewRecord
{
    protected static string $resource = AccountResource::class;

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
}
