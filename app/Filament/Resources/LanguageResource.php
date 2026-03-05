<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Models\Language;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class LanguageResource extends LmpResource
{
    protected static ?string $model = Language::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $modelLabel = 'filament.resources.language';

    protected static ?string $pluralModelLabel = 'filament.resources.languages';

    protected static ?string $recordTitleAttribute = 'id';

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
                Select::make('language_id')
                    ->label(__('filament.resources.language_fields.language'))
                    ->relationship('lmpLanguage', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.language_columns.id'))
                    ->sortable(),
                TextColumn::make('lmpLanguage.name')
                    ->label(__('filament.resources.language_columns.language'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lmpLanguage.code')
                    ->label(__('filament.resources.language_columns.code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'view' => Pages\ViewLanguage::route('/{record}'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
