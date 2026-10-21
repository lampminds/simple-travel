<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CrmCluster;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\CatDocument;
use App\Models\AccountType;
use App\Models\LmpCity;
use BackedEnum;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class AccountResource extends LmpResource
{
    protected static ?string $model = Account::class;

    protected static ?string $cluster = CrmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'filament.resources.account';

    protected static ?string $pluralModelLabel = 'filament.resources.accounts';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
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
                    Tab::make(__('filament.resources.account_tabs.main'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('nick')
                                    ->label(__('filament.resources.account_fields.nick'))
                                    ->placeholder(__('filament.resources.account_fields.nick'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('code')
                                    ->label(__('filament.resources.account_fields.code'))
                                    ->placeholder(__('filament.resources.account_fields.code'))
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('filament.resources.account_fields.code_help')),
                                TextInput::make('name')
                                    ->label(__('filament.resources.account_fields.name'))
                                    ->placeholder(__('filament.resources.account_fields.name'))
                                    ->required(fn (string $operation): bool => $operation === 'edit')
                                    ->maxLength(255),
                                TextInput::make('commercial_name')
                                    ->label(__('filament.resources.account_fields.commercial_name'))
                                    ->placeholder(__('filament.resources.account_fields.commercial_name'))
                                    ->required(fn (string $operation): bool => $operation === 'edit')
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label(__('filament.resources.account_fields.email'))
                                    ->placeholder(__('filament.resources.account_fields.email'))
                                    ->email()
                                    ->required(fn (string $operation): bool => $operation === 'edit')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('filament.resources.account_fields.phone'))
                                    ->placeholder(__('filament.resources.account_fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('address_line1')
                                    ->label(__('filament.resources.account_fields.address_line1'))
                                    ->placeholder(__('filament.resources.account_fields.address_line1'))
                                    ->maxLength(255),
                                TextInput::make('address_line2')
                                    ->label(__('filament.resources.account_fields.address_line2'))
                                    ->placeholder(__('filament.resources.account_fields.address_line2'))
                                    ->maxLength(255),
                                Select::make('city_id')
                                    ->label(__('filament.resources.account_fields.city_id'))
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return LmpCity::query()
                                            ->where(function (Builder $query) use ($search): void {
                                                $query->where('name', 'like', '%' . $search . '%');
                                                if (is_numeric($search)) {
                                                    $query->orWhere('id', (int) $search);
                                                }
                                            })
                                            ->orderBy('name')
                                            ->limit(50)
                                            ->pluck('name', 'id')
                                            ->all();
                                    })
                                    ->getOptionLabelUsing(fn ($value): ?string => $value ? LmpCity::query()->find($value)?->name : null)
                                    ->live()
                                    ->afterStateHydrated(function ($state, callable $set): void {
                                        self::setLocationLabelsFromCity($state, $set);
                                    })
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        self::setLocationLabelsFromCity($state, $set);
                                    })
                                    ->nullable(),
                                TextInput::make('state_name')
                                    ->label('Estado')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('country_name')
                                    ->label('País')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('postal_code')
                                    ->label(__('filament.resources.account_fields.postal_code'))
                                    ->placeholder(__('filament.resources.account_fields.postal_code'))
                                    ->maxLength(255),
                            ]),
                        ]),
                    Tab::make(__('filament.resources.account_tabs.tax_ids'))
                        ->schema([
                            Repeater::make('documents')
                                ->relationship()
                                ->schema([
                                    Select::make('document_id')
                                        ->label(__('filament.resources.account_document_fields.document_id'))
                                        ->options(
                                            fn () => CatDocument::query()
                                                ->byGroup('tax_id')
                                                ->where('active', true)
                                                ->with(['translations.language.locale'])
                                                ->ordered()
                                                ->get()
                                                ->mapWithKeys(fn ($c) => [$c->id => $c->name])
                                        )
                                        ->required()
                                        ->searchable(),
                                    TextInput::make('value')
                                        ->label(__('filament.resources.account_document_fields.value'))
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel(__('filament.resources.account_document_fields.add')),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.account_tabs.business_types'))
                        ->schema([
                            CheckboxList::make('accountTypes')
                                ->label(__('filament.resources.account_type_category_fields.label'))
                                ->helperText(__('filament.resources.account_type_category_fields.help'))
                                ->relationship(
                                    'accountTypes',
                                    'code',
                                    modifyQueryUsing: fn (Builder $query) => $query
                                        ->where('active', true)
                                        ->ordered()
                                        ->with(['translations.language.locale']),
                                )
                                ->getOptionLabelFromRecordUsing(
                                    fn (AccountType $record): string => $record->name ?: (string) $record->code
                                )
                                ->columns(2)
                                ->gridDirection('row')
                                ->columnSpanFull()
                                ->bulkToggleable(),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.account_tabs.portal'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('portal_host')
                                    ->label(__('filament.resources.account_fields.portal_host'))
                                    ->placeholder('b2b-test.ebgrouptravel.com')
                                    ->helperText(__('filament.resources.account_fields.portal_host_help'))
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string => Account::normalizePortalHost($state)
                                    )
                                    ->rules([
                                        fn (Get $get, ?Account $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                            self::validatePortalHost($get, $record, $value, $fail);
                                        },
                                    ]),
                                SpatieMediaLibraryFileUpload::make('portal_logo')
                                    ->label(__('filament.resources.account_fields.portal_logo'))
                                    ->helperText(__('filament.resources.account_fields.portal_logo_help'))
                                    ->collection(Account::MEDIA_COLLECTION_LOGO)
                                    ->disk('account_logos')
                                    ->conversion(Account::MEDIA_CONVERSION_LOGO_PREVIEW)
                                    ->image()
                                    ->maxSize(Account::MEDIA_MAX_LOGO_KB)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']),
                            ])->columns(2),
                        ])
                        ->visibleOn(['edit', 'view']),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.account_columns.id'))
                    ->sortable(),
                TextColumn::make('nick')
                    ->label(__('filament.resources.account_columns.nick'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.account_columns.code'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('filament.resources.account_columns.name'))
                    ->description(function (Account $record): ?HtmlString {
                        $commercialName = trim((string) $record->commercial_name);

                        if ($commercialName === '') {
                            return null;
                        }

                        return new HtmlString('<span style="font-style: italic">'.e($commercialName).'</span>');
                    })
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->where(function (Builder $query) use ($search): void {
                            $query->where('name', 'like', '%'.$search.'%')
                                ->orWhere('commercial_name', 'like', '%'.$search.'%');
                        });
                    })
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('filament.resources.account_columns.email'))
                    ->searchable(),
                TextColumn::make('portal_host')
                    ->label(__('filament.resources.account_columns.portal_host'))
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('accountType')
                    ->label(__('filament.resources.account_columns.account_type'))
                    ->relationship(
                        'accountTypes',
                        'code',
                        fn (Builder $query) => $query
                            ->where('active', true)
                            ->ordered()
                            ->with(['translations.language.locale'])
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (AccountType $record): string => $record->name ?: (string) $record->code
                    )
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view' => Pages\ViewAccount::route('/{record}'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * @param  mixed  $typeIds  Selected account type ids from the form
     */
    private static function formSelectsOperatorType(mixed $typeIds): bool
    {
        if (! is_array($typeIds) || $typeIds === []) {
            return false;
        }

        return AccountType::query()
            ->whereIn('id', $typeIds)
            ->where('code', 'operator')
            ->exists();
    }

    private static function validatePortalHost(Get $get, ?Account $record, mixed $value, Closure $fail): void
    {
        $host = Account::normalizePortalHost(is_string($value) ? $value : null);
        if ($host === null) {
            return;
        }

        if (! self::formSelectsOperatorType($get('accountTypes'))) {
            $fail(__('filament.resources.account_fields.portal_host_operator_only'));

            return;
        }

        if (! Account::isValidPortalHost($host)) {
            $fail(__('filament.resources.account_fields.portal_host_invalid'));

            return;
        }

        $taken = Account::query()
            ->where('portal_host', $host)
            ->when($record !== null, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
            ->exists();

        if ($taken) {
            $fail(__('filament.resources.account_fields.portal_host_taken'));
        }
    }

    private static function setLocationLabelsFromCity(mixed $cityId, callable $set): void
    {
        if (! is_numeric($cityId)) {
            $set('state_name', null);
            $set('country_name', null);

            return;
        }

        $city = LmpCity::query()
            ->with(['state.country'])
            ->find((int) $cityId);

        $set('state_name', $city?->state?->name);
        $set('country_name', $city?->state?->country?->name);
    }

}
