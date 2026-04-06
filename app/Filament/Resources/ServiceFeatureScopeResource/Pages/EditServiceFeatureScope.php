<?php

namespace App\Filament\Resources\ServiceFeatureScopeResource\Pages;

use App\Filament\Resources\ServiceFeatureScopeResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceFeatureScope extends LmpEditRecord
{
    protected static string $resource = ServiceFeatureScopeResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}

