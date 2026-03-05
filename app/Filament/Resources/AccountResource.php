<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\AccountCategory;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class AccountResource extends LmpResource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

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
                                    ->maxLength(255),
                                TextInput::make('commercial_name')
                                    ->label(__('filament.resources.account_fields.commercial_name'))
                                    ->placeholder(__('filament.resources.account_fields.commercial_name'))
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label(__('filament.resources.account_fields.email'))
                                    ->placeholder(__('filament.resources.account_fields.email'))
                                    ->email()
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
                                TextInput::make('postal_code')
                                    ->label(__('filament.resources.account_fields.postal_code'))
                                    ->placeholder(__('filament.resources.account_fields.postal_code'))
                                    ->maxLength(255),
                            ]),
                        ]),
                    Tab::make(__('filament.resources.account_tabs.tax_ids'))
                        ->schema([
                            Repeater::make('taxIds')
                                ->relationship()
                                ->schema([
                                    Select::make('account_category_id')
                                        ->label(__('filament.resources.account_tax_id_fields.account_category_id'))
                                        ->options(
                                            fn () => AccountCategory::query()
                                                ->byGroup('tax_id')
                                                ->with(['translations.language.lmpLanguage'])
                                                ->ordered()
                                                ->get()
                                                ->mapWithKeys(fn ($c) => [$c->id => $c->name])
                                        )
                                        ->required()
                                        ->searchable(),
                                    TextInput::make('value')
                                        ->label(__('filament.resources.account_tax_id_fields.value'))
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel(__('filament.resources.account_tax_id_fields.add')),
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
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.account_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('commercial_name')
                    ->label(__('filament.resources.account_columns.commercial_name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.resources.account_columns.email'))
                    ->searchable(),
            ])
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
}
