<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditLanguage extends LmpEditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
