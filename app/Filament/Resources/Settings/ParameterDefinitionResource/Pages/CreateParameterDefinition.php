<?php

namespace App\Filament\Resources\Settings\ParameterDefinitionResource\Pages;

use App\Filament\Resources\Settings\ParameterDefinitionResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateParameterDefinition extends LmpCreateRecord
{
    protected static string $resource = ParameterDefinitionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}

