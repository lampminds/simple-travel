<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Models\ContactDepartment;
use App\Models\ContactPosition;
use App\Models\ContactType;
use App\Models\Person;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PersonResource extends LmpResource
{
    protected static ?string $model = Person::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 12;

    protected static ?string $modelLabel = 'filament.resources.person';

    protected static ?string $pluralModelLabel = 'filament.resources.persons';

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

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.person_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('name')
                                    ->label(__('filament.resources.person_fields.name'))
                                    ->required()
                                    ->maxLength(255),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.person_tabs.users'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('userPersons')
                                        ->relationship()
                                        ->schema([
                                            Select::make('user_id')
                                                ->label(__('filament.resources.person_fields.user_id'))
                                                ->relationship('user', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.person_fields.add_user_link'))
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.person_tabs.account_memberships'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('accountPersons')
                                        ->relationship()
                                        ->schema([
                                            Select::make('account_id')
                                                ->label(__('filament.resources.person_fields.account_id'))
                                                ->relationship('account', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Select::make('contact_department_id')
                                                ->label(__('filament.resources.person_fields.contact_department_id'))
                                                ->options(
                                                    fn () => ContactDepartment::query()
                                                        ->where('active', true)
                                                        ->orderBy('sort_order')
                                                        ->get()
                                                        ->mapWithKeys(fn (ContactDepartment $d) => [$d->id => $d->code])
                                                )
                                                ->searchable()
                                                ->required(),
                                            Select::make('contact_position_id')
                                                ->label(__('filament.resources.person_fields.contact_position_id'))
                                                ->options(
                                                    fn () => ContactPosition::query()
                                                        ->where('active', true)
                                                        ->orderBy('sort_order')
                                                        ->get()
                                                        ->mapWithKeys(fn (ContactPosition $p) => [$p->id => $p->code])
                                                )
                                                ->searchable()
                                                ->required(),
                                            Toggle::make('is_primary')
                                                ->label(__('filament.resources.person_fields.is_primary'))
                                                ->default(false),
                                            Toggle::make('is_active')
                                                ->label(__('filament.common.active'))
                                                ->default(true),
                                            Toggle::make('is_public_contact')
                                                ->label(__('filament.resources.person_fields.is_public_contact'))
                                                ->default(false),
                                            Toggle::make('is_preferred_contact_mode')
                                                ->label(__('filament.resources.person_fields.is_preferred_contact_mode'))
                                                ->default(false),
                                        ])
                                        ->columns(3)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.person_fields.add_account_membership'))
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.person_tabs.contact_methods'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('contactMethods')
                                        ->relationship()
                                        ->schema([
                                            Select::make('contact_type_id')
                                                ->label(__('filament.resources.person_fields.contact_type_id'))
                                                ->options(
                                                    fn () => ContactType::query()
                                                        ->where('active', true)
                                                        ->orderBy('sort_order')
                                                        ->get()
                                                        ->mapWithKeys(
                                                            fn (ContactType $t) => [
                                                                $t->id => $t->getRawOriginal('code') ?? (string) $t->id,
                                                            ]
                                                        )
                                                )
                                                ->searchable()
                                                ->required(),
                                            TextInput::make('value')
                                                ->label(__('filament.resources.person_fields.contact_method_value'))
                                                ->required()
                                                ->maxLength(255),
                                            Toggle::make('is_primary')
                                                ->label(__('filament.resources.person_fields.contact_method_is_primary'))
                                                ->default(false),
                                            Toggle::make('is_verified')
                                                ->label(__('filament.resources.person_fields.is_verified'))
                                                ->default(false),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.person_fields.add_contact_method'))
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.person_tabs.contact_links'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('contactLinks')
                                        ->relationship()
                                        ->schema([
                                            Select::make('account_id')
                                                ->label(__('filament.resources.person_fields.link_account_id'))
                                                ->relationship('account', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Select::make('source_account_id')
                                                ->label(__('filament.resources.person_fields.link_source_account_id'))
                                                ->relationship('sourceAccount', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->required(),
                                            Toggle::make('is_favorite')
                                                ->label(__('filament.resources.person_fields.is_favorite'))
                                                ->default(false),
                                            Select::make('visibility')
                                                ->label(__('filament.resources.person_fields.visibility'))
                                                ->options([
                                                    'private' => __('filament.resources.person_visibility.private'),
                                                    'shared' => __('filament.resources.person_visibility.shared'),
                                                ])
                                                ->required()
                                                ->default('private'),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.person_fields.add_contact_link'))
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->visibleOn(['edit', 'view']),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(
                fn (Builder $query) => $query->withCount(['users', 'accountPersons', 'contactMethods', 'contactLinks'])
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.person_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.person_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('filament.resources.person_columns.users_count'))
                    ->sortable(),
                TextColumn::make('account_persons_count')
                    ->label(__('filament.resources.person_columns.account_memberships_count'))
                    ->sortable(),
                TextColumn::make('contact_methods_count')
                    ->label(__('filament.resources.person_columns.contact_methods_count'))
                    ->sortable(),
                TextColumn::make('contact_links_count')
                    ->label(__('filament.resources.person_columns.contact_links_count'))
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersons::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'view' => Pages\ViewPerson::route('/{record}'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
