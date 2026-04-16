<?php

namespace App\Filament\Resources\TodoTaskResource\Pages;

use App\Filament\Resources\Pages\BaseListRecords;
use App\Filament\Resources\TodoTaskResource;

class ListTodoTasks extends BaseListRecords
{
    protected static string $resource = TodoTaskResource::class;
}
