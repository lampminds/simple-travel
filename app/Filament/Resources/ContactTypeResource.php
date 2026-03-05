<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactTypeResource\Pages;
use App\Models\ContactType;
use App\Models\Language;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ContactTypeResource extends LmpResource
{
    protected static ?string $model = ContactType::class;

    public static function form(Schema $schema): Schema
    {
        if (property_exists(static::getModel(), 'dont_use_audit')) {
            return $schema->schema(
                array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema)),
            );
        }

        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'filament.resources.contact_type';

    protected static ?string $pluralModelLabel = 'filament.resources.contact_types';

    protected static ?string $recordTitleAttribute = 'code';

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
        $languages = Language::query()->with('lmpLanguage')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.contact_type_fields.name'))
                        ->maxLength(255),
                    TextInput::make("translations.{$lang->id}.mask")
                        ->label(__('filament.resources.contact_type_fields.mask'))
                        ->maxLength(255)
                        ->helperText(__('filament.resources.contact_type_fields.mask_help')),
                    TextInput::make("translations.{$lang->id}.validation")
                        ->label(__('filament.resources.contact_type_fields.validation'))
                        ->maxLength(255)
                        ->helperText(__('filament.resources.contact_type_fields.validation_help')),
                    Toggle::make("translations.{$lang->id}.active")
                        ->label(__('filament.common.active')),
                ])
                ->columns(4)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.account_category_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.contact_type_fields.code'))
                                    ->placeholder(__('filament.resources.contact_type_fields.code'))
                                    ->maxLength(255),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.account_category_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.contact_type_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.contact_type_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mask')
                    ->label(__('filament.resources.contact_type_columns.mask'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('validation')
                    ->label(__('filament.resources.contact_type_columns.validation'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.lmpLanguage']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactTypes::route('/'),
            'create' => Pages\CreateContactType::route('/create'),
            'view' => Pages\ViewContactType::route('/{record}'),
            'edit' => Pages\EditContactType::route('/{record}/edit'),
        ];
    }
}
