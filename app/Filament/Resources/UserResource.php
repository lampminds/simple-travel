<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class UserResource extends LmpResource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'filament.resources.user';

    protected static ?string $pluralModelLabel = 'filament.resources.users';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')->schema([
                Select::make('account_id')
                    ->label(__('filament.resources.user_fields.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('name')
                    ->label(__('filament.resources.user_fields.name'))
                    ->placeholder(__('filament.resources.user_fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('filament.resources.user_fields.email'))
                    ->placeholder(__('filament.resources.user_fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('filament.resources.user_fields.password'))
                    ->placeholder(__('filament.resources.user_fields.password'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->hiddenOn('edit'),
                Select::make('roles')
                    ->label(__('filament.resources.user_fields.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(false),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.user_columns.id'))
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label(__('filament.resources.user_columns.account'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.user_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('filament.resources.user_columns.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles_list')
                    ->label(__('filament.resources.user_columns.roles'))
                    ->getStateUsing(fn (User $record) => $record->roles->pluck('name')->join(', '))
                    ->badge(),
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
