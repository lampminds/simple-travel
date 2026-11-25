<?php

namespace App\Filament\Pages;

use App\Support\KbPortal;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\RedirectResponse;

class TicketsUnavailablePage extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'tickets';

    public function mount(): ?RedirectResponse
    {
        if (KbPortal::isConfigured()) {
            return redirect()->route('kb.open');
        }

        return null;
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament.panel.tickets');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.panel.tickets_unavailable'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->iconColor('warning'),
            ]);
    }
}
