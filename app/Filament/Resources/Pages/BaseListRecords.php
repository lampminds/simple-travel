<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Support\FilamentIntroHelp;
use Filament\Actions;
use Illuminate\Support\Str;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpListRecords;
use Livewire\Attributes\Computed;

/**
 * List records page that shows a translated record count next to the Create button.
 * Lampminds' HasFilteredRecordCount builds the label from the model class name (e.g. "serviceplans");
 * we override to use the Resource's getPluralModelLabel() and a translated "Total".
 * Create button label is just "Crear" / "Create" (no model name) to avoid redundancy.
 *
 * Intro help icon: {@see FilamentIntroHelp} using `getFilamentListIntroHelpSlug()` and
 * `getFilamentListIntroHelpDefaultTitle()` (override when the auto slug does not match lang keys).
 */
abstract class BaseListRecords extends LmpListRecords
{
    /**
     * Translation slug under {@code filament_help.{slug}.list.*} (defaults from resource class name).
     */
    protected static function getFilamentListIntroHelpSlug(): string
    {
        $resource = static::$resource;
        $basename = class_basename($resource);
        $short = str_ends_with($basename, 'Resource')
            ? substr($basename, 0, -strlen('Resource'))
            : $basename;

        return Str::snake($short);
    }

    /**
     * Default tooltip title (used when {@code intro_help_aria_label} is absent or empty in filament_help).
     */
    protected function getFilamentListIntroHelpDefaultTitle(): string
    {
        return static::getResource()::getPluralModelLabel();
    }

    protected function getHeaderActions(): array
    {
        return array_merge(
            [
                FilamentIntroHelp::makeListHeaderAction(
                    static::getFilamentListIntroHelpSlug(),
                    $this->getFilamentListIntroHelpDefaultTitle(),
                ),
            ],
            $this->getCoreListHeaderActions(),
        );
    }

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    protected function getCoreListHeaderActions(): array
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
