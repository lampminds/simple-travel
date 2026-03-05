<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactDepartmentResource\Pages;
use App\Models\ContactDepartment;
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

class ContactDepartmentResource extends LmpResource
{
    protected static ?string $model = ContactDepartment::class;

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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $modelLabel = 'filament.resources.contact_department';

    protected static ?string $pluralModelLabel = 'filament.resources.contact_departments';

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
                    TextInput::make("translations.{$lang->id}.code")
                        ->label(__('filament.resources.contact_department_fields.code'))
                        ->maxLength(255),
                    Toggle::make("translations.{$lang->id}.active")
                        ->label(__('filament.common.active')),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.account_category_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.contact_department_fields.code'))
                                    ->placeholder(__('filament.resources.contact_department_fields.code'))
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
                    ->label(__('filament.resources.contact_department_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.contact_department_columns.code'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.lmpLanguage']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactDepartments::route('/'),
            'create' => Pages\CreateContactDepartment::route('/create'),
            'view' => Pages\ViewContactDepartment::route('/{record}'),
            'edit' => Pages\EditContactDepartment::route('/{record}/edit'),
        ];
    }
}
