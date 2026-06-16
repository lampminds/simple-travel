<?php

namespace App\Filament\Resources\AiUsageLogResource\Pages;

use App\Filament\Resources\AiUsageLogResource;
use App\Filament\Resources\Pages\BaseListRecords;

class ListAiUsageLogs extends BaseListRecords
{
    protected static string $resource = AiUsageLogResource::class;

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getCoreListHeaderActions(): array
    {
        if ($this->showFilteredRecordCount) {
            return [$this->getRecordCountAction()];
        }

        return [];
    }
}
