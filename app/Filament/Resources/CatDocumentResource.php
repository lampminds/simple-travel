<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\SystemTablesCluster;
use App\Filament\Resources\CatDocumentResource\Pages;
use App\Models\CatDocument;
use App\Models\Language;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CatDocumentResource extends BaseResource
{
    protected static ?string $model = CatDocument::class;

    protected static ?string $cluster = SystemTablesCluster::class;

    public static function form(Schema $schema): Schema
    {
        if (property_exists(static::getModel(), 'dont_use_audit')) {
            return $schema->schema(
                array_map(
                    fn ($c) => $c->columnSpanFull(),
                    static::getMainFormSchema($schema),
                ),
            );
        }

        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'filament.resources.cat_document';

    protected static ?string $pluralModelLabel = 'filament.resources.cat_documents';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?int $navigationSort = 18;

    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }

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
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.cat_document_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.cat_document_fields.description'))
                        ->rows(1),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.cat_document_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('group')
                                    ->label(__('filament.resources.cat_document_fields.group'))
                                    ->placeholder(__('filament.resources.cat_document_fields.group'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->label(__('filament.resources.cat_document_fields.code'))
                                    ->placeholder(__('filament.resources.cat_document_fields.code'))
                                    ->maxLength(255),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.cat_document_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.cat_document_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('group')
                    ->label(__('filament.resources.cat_document_columns.group'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('raw_code')
                    ->label(__('filament.resources.cat_document_columns.code'))
                    ->getStateUsing(fn (CatDocument $record): ?string => $record->getRawOriginal('code'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->where('code', 'like', '%'.$search.'%');
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('code', $direction)),
                TextColumn::make('name')
                    ->label(__('filament.resources.cat_document_columns.name'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
                TextColumn::make('description')
                    ->label(__('filament.resources.cat_document_columns.description'))
                    ->limit(40)
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('filament.resources.cat_document_columns.group'))
                    ->options(fn () => CatDocument::query()
                        ->where('active', true)
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->toArray()),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['translations.language.locale']))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%' . $search . '%';
        $query->orWhereHas('translations', function ($q) use ($term): void {
            $q->where('name', 'like', $term);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatDocuments::route('/'),
            'create' => Pages\CreateCatDocument::route('/create'),
            'view' => Pages\ViewCatDocument::route('/{record}'),
            'edit' => Pages\EditCatDocument::route('/{record}/edit'),
        ];
    }
}
