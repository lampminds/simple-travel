<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\Pages\BaseListRecords;

class ListUsers extends BaseListRecords
{
    protected static string $resource = UserResource::class;
}
