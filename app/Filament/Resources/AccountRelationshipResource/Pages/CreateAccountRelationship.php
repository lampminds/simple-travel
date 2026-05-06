<?php

namespace App\Filament\Resources\AccountRelationshipResource\Pages;

use App\Filament\Resources\AccountRelationshipResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateAccountRelationship extends LmpCreateRecord
{
    protected static string $resource = AccountRelationshipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}

