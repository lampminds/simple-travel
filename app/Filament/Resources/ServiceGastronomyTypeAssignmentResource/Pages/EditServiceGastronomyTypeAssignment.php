<?php

namespace App\Filament\Resources\ServiceGastronomyTypeAssignmentResource\Pages;

use App\Filament\Resources\ServiceGastronomyTypeAssignmentResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpEditRecord;

class EditServiceGastronomyTypeAssignment extends LmpEditRecord
{
    protected static string $resource = ServiceGastronomyTypeAssignmentResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('index');
    }
}
