<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CrmCluster;
use App\Filament\Resources\AccountRelationshipResource\Pages;
use App\Models\AccountRelationship;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class AccountRelationshipResource extends LmpResource
{
    protected static ?string $model = AccountRelationship::class;

    protected static ?string $cluster = CrmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?int $navigationSort = 99;

    protected static ?string $modelLabel = 'filament.resources.account_relationship';

    protected static ?string $pluralModelLabel = 'filament.resources.account_relationships';

    protected static ?string $recordTitleAttribute = 'id';

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
        return AccountResource::getNavigationGroup();
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')
                ->schema([
                    Select::make('operator_account_id')
                        ->label(__('filament.resources.account_relationship_fields.operator_account_id'))
                        ->relationship('operatorAccount', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('provider_account_id')
                        ->label(__('filament.resources.account_relationship_fields.provider_account_id'))
                        ->relationship('providerAccount', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('status')
                        ->label(__('filament.resources.account_relationship_fields.status'))
                        ->options([
                            AccountRelationship::STATUS_APPROVED => __('filament.resources.account_relationship_status.approved'),
                            AccountRelationship::STATUS_SUSPENDED => __('filament.resources.account_relationship_status.suspended'),
                            AccountRelationship::STATUS_TERMINATED => __('filament.resources.account_relationship_status.terminated'),
                        ])
                        ->required(),
                    Select::make('created_via')
                        ->label(__('filament.resources.account_relationship_fields.created_via'))
                        ->options([
                            AccountRelationship::CREATED_VIA_INVITATION => __('filament.resources.account_relationship_created_via.invitation'),
                            AccountRelationship::CREATED_VIA_MANUAL => __('filament.resources.account_relationship_created_via.manual'),
                            AccountRelationship::CREATED_VIA_SYSTEM => __('filament.resources.account_relationship_created_via.system'),
                        ])
                        ->required(),
                    Select::make('source_invitation_id')
                        ->label(__('filament.resources.account_relationship_fields.source_invitation_id'))
                        ->relationship('sourceInvitation', 'id')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('approved_by_user_id')
                        ->label(__('filament.resources.account_relationship_fields.approved_by_user_id'))
                        ->relationship('approvedByUser', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    DateTimePicker::make('approved_at')
                        ->label(__('filament.resources.account_relationship_fields.approved_at'))
                        ->nullable(),
                    DateTimePicker::make('suspended_at')
                        ->label(__('filament.resources.account_relationship_fields.suspended_at'))
                        ->nullable(),
                    DateTimePicker::make('terminated_at')
                        ->label(__('filament.resources.account_relationship_fields.terminated_at'))
                        ->nullable(),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['operatorAccount', 'providerAccount'])->orderByDesc('id'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.account_relationship_columns.id'))
                    ->sortable(),
                TextColumn::make('operatorAccount.name')
                    ->label(__('filament.resources.account_relationship_columns.operator_account'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('providerAccount.name')
                    ->label(__('filament.resources.account_relationship_columns.provider_account'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament.resources.account_relationship_columns.status'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('filament.resources.account_relationship_status.'.$state) : '—')
                    ->badge(),
                TextColumn::make('created_via')
                    ->label(__('filament.resources.account_relationship_columns.created_via'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('filament.resources.account_relationship_created_via.'.$state) : '—')
                    ->badge(),
                TextColumn::make('approved_at')
                    ->label(__('filament.resources.account_relationship_columns.approved_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.resources.account_relationship_filters.status'))
                    ->options([
                        AccountRelationship::STATUS_APPROVED => __('filament.resources.account_relationship_status.approved'),
                        AccountRelationship::STATUS_SUSPENDED => __('filament.resources.account_relationship_status.suspended'),
                        AccountRelationship::STATUS_TERMINATED => __('filament.resources.account_relationship_status.terminated'),
                    ]),
                SelectFilter::make('created_via')
                    ->label(__('filament.resources.account_relationship_filters.created_via'))
                    ->options([
                        AccountRelationship::CREATED_VIA_INVITATION => __('filament.resources.account_relationship_created_via.invitation'),
                        AccountRelationship::CREATED_VIA_MANUAL => __('filament.resources.account_relationship_created_via.manual'),
                        AccountRelationship::CREATED_VIA_SYSTEM => __('filament.resources.account_relationship_created_via.system'),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountRelationships::route('/'),
            'create' => Pages\CreateAccountRelationship::route('/create'),
            'edit' => Pages\EditAccountRelationship::route('/{record}/edit'),
        ];
    }
}

