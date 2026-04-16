<?php

namespace App\Filament\Resources\UserInvitationResource\Pages;

use App\Filament\Resources\UserInvitationResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditUserInvitation extends LmpEditRecord
{
    protected static string $resource = UserInvitationResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}

