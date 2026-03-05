<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class LatestSignupsWidget extends BaseTableWidget
{
    protected static ?string $heading = 'Últimos registros';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('account')
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('account.name')
                    ->label('Cuenta')
                    ->placeholder('—'),
                TextColumn::make('email_verified_at')
                    ->label('Cuenta confirmada')
                    ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->formatStateUsing(function ($state): string | Htmlable | null {
                        if (! $state) {
                            return '—';
                        }
                        return new \Illuminate\Support\HtmlString(
                            e($state->format('d/m/Y H:i')) . '<br><span class="text-muted text-sm">' . e($state->diffForHumans()) . '</span>'
                        );
                    })
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
