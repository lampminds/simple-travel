<?php

namespace App\Filament\Resources\ServiceGastronomyTypeAssignmentResource\Pages;

use App\Filament\Resources\ServiceGastronomyTypeAssignmentResource;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpCreateRecord;

class CreateServiceGastronomyTypeAssignment extends LmpCreateRecord
{
    protected static string $resource = ServiceGastronomyTypeAssignmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResourceUrl('index');
    }
}
