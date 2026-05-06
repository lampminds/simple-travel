<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\Pages\BaseListRecords;
use App\Filament\Resources\PersonResource;

class ListPersons extends BaseListRecords
{
    protected static string $resource = PersonResource::class;
}
