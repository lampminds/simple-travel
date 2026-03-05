<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Models\Provider;
use App\Models\ProviderCategory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ProviderResource extends LmpResource
{
    protected static ?string $model = Provider::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $modelLabel = 'filament.resources.provider';

    protected static ?string $pluralModelLabel = 'filament.resources.providers';

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
                    Tab::make(__('filament.resources.provider_tabs.main'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('name')
                                    ->label(__('filament.resources.provider_fields.name'))
                                    ->placeholder(__('filament.resources.provider_fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('commercial_name')
                                    ->label(__('filament.resources.provider_fields.commercial_name'))
                                    ->placeholder(__('filament.resources.provider_fields.commercial_name'))
                                    ->maxLength(255),
                                Select::make('status')
                                    ->label(__('filament.resources.provider_fields.status'))
                                    ->options([
                                        'active' => __('filament.resources.provider_status.active'),
                                        'onhold' => __('filament.resources.provider_status.onhold'),
                                        'inactive' => __('filament.resources.provider_status.inactive'),
                                        'terminated' => __('filament.resources.provider_status.terminated'),
                                    ])
                                    ->default('active')
                                    ->required(),
                                Select::make('inviting_id')
                                    ->label(__('filament.resources.provider_fields.inviting_id'))
                                    ->relationship('inviting', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                TextInput::make('email')
                                    ->label(__('filament.resources.provider_fields.email'))
                                    ->placeholder(__('filament.resources.provider_fields.email'))
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('filament.resources.provider_fields.phone'))
                                    ->placeholder(__('filament.resources.provider_fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('address_line1')
                                    ->label(__('filament.resources.provider_fields.address_line1'))
                                    ->placeholder(__('filament.resources.provider_fields.address_line1'))
                                    ->maxLength(255),
                                TextInput::make('address_line2')
                                    ->label(__('filament.resources.provider_fields.address_line2'))
                                    ->placeholder(__('filament.resources.provider_fields.address_line2'))
                                    ->maxLength(255),
                                Select::make('city_id')
                                    ->label(__('filament.resources.provider_fields.city_id'))
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                TextInput::make('postal_code')
                                    ->label(__('filament.resources.provider_fields.postal_code'))
                                    ->placeholder(__('filament.resources.provider_fields.postal_code'))
                                    ->maxLength(255),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.provider_tabs.categories'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('categories')
                                    ->label(__('filament.resources.provider_fields.categories'))
                                    ->relationship(
                                        'categories',
                                        'name',
                                        fn ($query) => $query->ordered()
                                    )
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),
                            ]),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.provider_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.provider_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('commercial_name')
                    ->label(__('filament.resources.provider_columns.commercial_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament.resources.provider_columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("filament.resources.provider_status.{$state}")),
                TextColumn::make('inviting.name')
                    ->label(__('filament.resources.provider_columns.inviting'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('email')
                    ->label(__('filament.resources.provider_columns.email'))
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.resources.provider_columns.status'))
                    ->options([
                        'active' => __('filament.resources.provider_status.active'),
                        'onhold' => __('filament.resources.provider_status.onhold'),
                        'inactive' => __('filament.resources.provider_status.inactive'),
                        'terminated' => __('filament.resources.provider_status.terminated'),
                    ]),
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'view' => Pages\ViewProvider::route('/{record}'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
