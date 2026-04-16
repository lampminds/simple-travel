<?php

namespace App\Filament\Resources\TodoCategoryResource\Pages;

use App\Filament\Resources\Pages\BaseListRecords;
use App\Filament\Resources\TodoCategoryResource;

class ListTodoCategories extends BaseListRecords
{
    protected static string $resource = TodoCategoryResource::class;
}
