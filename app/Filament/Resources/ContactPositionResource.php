<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactPositionResource\Pages;
use App\Models\ContactPosition;
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

class ContactPositionResource extends LmpResource
{
    protected static ?string $model = ContactPosition::class;

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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $modelLabel = 'filament.resources.contact_position';

    protected static ?string $pluralModelLabel = 'filament.resources.contact_positions';

    protected static ?string $recordTitleAttribute = 'code';

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
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.contact_position_fields.name'))
                        ->maxLength(255),
                ])
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.contact_position_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.contact_position_fields.code'))
                                    ->placeholder(__('filament.resources.contact_position_fields.code'))
                                    ->maxLength(255),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.contact_position_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.contact_position_columns.id'))
                    ->sortable(),
                TextColumn::make('raw_code')
                    ->label(__('filament.resources.contact_position_columns.code'))
                    ->getStateUsing(fn ($record) => $record->getRawOriginal('code'))
                    ->searchable(query: function ($query, string $search): void {
                        $query->where('code', 'like', '%' . $search . '%');
                    })
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('code', $direction)),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.contact_position_columns.name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale']));
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactPositions::route('/'),
            'create' => Pages\CreateContactPosition::route('/create'),
            'view' => Pages\ViewContactPosition::route('/{record}'),
            'edit' => Pages\EditContactPosition::route('/{record}/edit'),
        ];
    }
}
