<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use App\Models\ContactPosition;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ContactResource extends LmpResource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'filament.resources.contact';

    protected static ?string $pluralModelLabel = 'filament.resources.contacts';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_contacts';

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
        return $group instanceof \UnitEnum ? $group->value : ($group !== null ? (string) __($group) : null);
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')->schema([
                Select::make('account_id')
                    ->label(__('filament.resources.contact_fields.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label(__('filament.resources.contact_fields.name'))
                    ->placeholder(__('filament.resources.contact_fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('contact_department_id')
                    ->label(__('filament.resources.contact_fields.contact_department_id'))
                    ->relationship('department', 'code', fn ($query) => $query->where('active', true))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('contact_position_id')
                    ->label(__('filament.resources.contact_fields.contact_position_id'))
                    ->options(fn () => ContactPosition::query()->where('active', true)->with(['translations.language.locale'])->orderBy('sort_order')->get()->pluck('code', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.contact_columns.id'))
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label(__('filament.resources.contact_columns.account'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.contact_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.code')
                    ->label(__('filament.resources.contact_columns.department'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position.code')
                    ->label(__('filament.resources.contact_columns.position'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with(['account', 'department', 'position']));
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'view' => Pages\ViewContact::route('/{record}'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
