<?php

namespace App\Filament\Resources\AccountRelationshipResource\Pages;

use App\Filament\Resources\AccountRelationshipResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditAccountRelationship extends LmpEditRecord
{
    protected static string $resource = AccountRelationshipResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}

