<?php

namespace App\Filament\Resources\Pages;

use Filament\Actions;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpListRecords;
use Livewire\Attributes\Computed;

/**
 * List records page that shows a translated record count next to the Create button.
 * Lampminds' HasFilteredRecordCount builds the label from the model class name (e.g. "serviceplans");
 * we override to use the Resource's getPluralModelLabel() and a translated "Total".
 * Create button label is just "Crear" / "Create" (no model name) to avoid redundancy.
 */
abstract class BaseListRecords extends LmpListRecords
{
    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make()
                ->label(__('filament-actions::create.single.modal.actions.create.label')),
        ];

        if ($this->showFilteredRecordCount) {
            $actions[] = $this->getRecordCountAction();
        }

        return $actions;
    }

    #[Computed]
    public function getFilteredRecordCount(): string
    {
        $count = $this->getFilteredTableQuery()->count();
        $pluralLabel = static::getResource()::getPluralModelLabel();

        return __('filament.pages.list_records_count', [
            'count' => number_format($count),
            'label' => $pluralLabel,
        ]);
    }
}
