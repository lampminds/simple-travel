<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateAccount extends LmpCreateRecord
{
    protected static string $resource = AccountResource::class;

    /** @var array{address_line1: mixed, address_line2: mixed, city_id: mixed, postal_code: mixed}|null */
    private ?array $primaryAddressInput = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);
        $this->primaryAddressInput = Account::pullAddressInput($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        parent::afterCreate();

        if ($this->record instanceof Account) {
            $this->record->syncPrimaryAddress($this->primaryAddressInput ?? []);
        }
    }
}
