<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Pages\WebsiteMenuEditorPage;
use App\Filament\Resources\MenuResource;
use App\Filament\Resources\Pages\BaseListRecords;
use Filament\Actions;

class ListMenus extends BaseListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([
            Actions\Action::make('websiteMenuEditor')
                ->label(__('filament.pages.website_menu_editor.header_action'))
                ->url(WebsiteMenuEditorPage::getUrl())
                ->icon('heroicon-o-squares-2x2'),
        ], parent::getHeaderActions());
    }
}
