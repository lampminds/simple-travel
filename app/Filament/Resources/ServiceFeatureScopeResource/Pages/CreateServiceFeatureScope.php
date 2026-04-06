<?php

namespace App\Filament\Resources\ServiceFeatureScopeResource\Pages;

use App\Filament\Resources\ServiceFeatureScopeResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceFeatureScope extends LmpCreateRecord
{
    protected static string $resource = ServiceFeatureScopeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}

