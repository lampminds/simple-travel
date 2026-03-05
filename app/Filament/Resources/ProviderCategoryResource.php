<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderCategoryResource\Pages;
use App\Models\ProviderCategory;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ProviderCategoryResource extends LmpResource
{
    protected static ?string $model = ProviderCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'filament.resources.provider_category';

    protected static ?string $pluralModelLabel = 'filament.resources.provider_categories';

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
                TextInput::make('group')
                    ->label(__('filament.resources.provider_category_fields.group'))
                    ->placeholder(__('filament.resources.provider_category_fields.group'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label(__('filament.resources.provider_category_fields.code'))
                    ->placeholder(__('filament.resources.provider_category_fields.code'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label(__('filament.resources.provider_category_fields.name'))
                    ->placeholder(__('filament.resources.provider_category_fields.name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('filament.resources.provider_category_fields.description'))
                    ->placeholder(__('filament.resources.provider_category_fields.description'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.provider_category_columns.id'))
                    ->sortable(),
                TextColumn::make('group')
                    ->label(__('filament.resources.provider_category_columns.group'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.provider_category_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.provider_category_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('filament.resources.provider_category_columns.description'))
                    ->limit(40)
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('filament.resources.provider_category_columns.group'))
                    ->options(fn () => ProviderCategory::query()
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->toArray()),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderCategories::route('/'),
            'create' => Pages\CreateProviderCategory::route('/create'),
            'view' => Pages\ViewProviderCategory::route('/{record}'),
            'edit' => Pages\EditProviderCategory::route('/{record}/edit'),
        ];
    }
}
