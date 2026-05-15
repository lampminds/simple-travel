<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class LatestLoginsWidget extends BaseTableWidget
{
    protected static ?string $heading = 'Últimos accesos';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('accounts')
                    ->whereNotNull('last_login_at')
                    ->latest('last_login_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('accounts_display')
                    ->label('Cuenta')
                    ->getStateUsing(function (User $record): string {
                        $names = $record->accounts->pluck('commercial_name')->filter();
                        if ($names->isEmpty()) {
                            $names = $record->accounts->pluck('name')->filter();
                        }

                        return $names->isEmpty() ? '—' : $names->join(', ');
                    }),
                TextColumn::make('last_login_at')
                    ->label('Fecha de acceso')
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
