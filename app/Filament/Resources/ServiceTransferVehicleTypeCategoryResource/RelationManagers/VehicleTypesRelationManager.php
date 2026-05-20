<?php

namespace App\Filament\Resources\ServiceTransferVehicleTypeCategoryResource\RelationManagers;

use App\Models\Account;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VehicleTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicleTypes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return (string) __('filament.resources.service_transfer_vehicle_type_category_relation.vehicle_types_tab');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('')->schema([
                Select::make('account_id')
                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.account_id'))
                    ->options(fn () => Account::query()->orderBy('id')->pluck('nick', 'id')->all())
                    ->default(1)
                    ->required()
                    ->searchable(),
                TextInput::make('code')
                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.code'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('max_passengers')
                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.max_passengers'))
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('max_luggage')
                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.max_luggage'))
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),
                Toggle::make('active')
                    ->label(__('filament.common.active'))
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account.nick')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.account'))
                    ->sortable(),
                TextColumn::make('max_passengers')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.max_passengers'))
                    ->sortable(),
                TextColumn::make('max_luggage')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.max_luggage'))
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with('account'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $owner = $this->getOwnerRecord();
                        if (! array_key_exists('account_id', $data) || $data['account_id'] === '' || $data['account_id'] === null) {
                            $data['account_id'] = null;
                        }
                        $data['active'] = $data['active'] ?? true;
                        $data['sort_order'] = (int) ($owner->vehicleTypes()->max('sort_order') ?? 0) + 1;

                        return $data;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }
}
