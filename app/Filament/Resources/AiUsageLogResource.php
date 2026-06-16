<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\AiUsageLogResource\Pages;
use App\Models\AiAssistantMessage;
use App\Services\AiUsageCostCalculator;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AiUsageLogResource extends BaseResource
{
    protected static ?string $model = AiAssistantMessage::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $modelLabel = 'filament.resources.ai_usage_log';

    protected static ?string $pluralModelLabel = 'filament.resources.ai_usage_logs';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_ai';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
    }

    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('filament.resources.ai_usage_log_columns.created_at'))
                    ->formatStateUsing(fn ($state) => $state ? locale_datetime($state) : '—')
                    ->sortable(),
                TextColumn::make('usage_type')
                    ->label(__('filament.resources.ai_usage_log_columns.usage_type'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        AiAssistantMessage::USAGE_ASSISTANT => __('filament.resources.ai_usage_log_types.assistant'),
                        AiAssistantMessage::USAGE_TRANSLATION => __('filament.resources.ai_usage_log_types.translation'),
                        AiAssistantMessage::USAGE_OPENAI_TRANSLATION => __('filament.resources.ai_usage_log_types.openai_translation'),
                        default => (string) $state,
                    })
                    ->badge()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('filament.resources.ai_usage_log_columns.user'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label(__('filament.resources.ai_usage_log_columns.account'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_tokens')
                    ->label(__('filament.resources.ai_usage_log_columns.total_tokens'))
                    ->numeric()
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('estimated_usd')
                    ->label(__('filament.resources.ai_usage_log_columns.estimated_usd'))
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(function (AiAssistantMessage $record): string {
                        $usd = AiUsageCostCalculator::forRecord($record);

                        return '$'.number_format($usd, 6);
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('filament.resources.ai_usage_log_filters.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('account_id')
                    ->label(__('filament.resources.ai_usage_log_filters.account'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->label(__('filament.resources.ai_usage_log_filters.date_range'))
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('filament.resources.ai_usage_log_filters.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('filament.resources.ai_usage_log_filters.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'account']))
            ->recordActions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiUsageLogs::route('/'),
        ];
    }
}
