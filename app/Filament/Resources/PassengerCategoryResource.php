<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\PassengerCategoryResource\Pages;
use App\Models\Language;
use App\Models\PassengerCategory;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PassengerCategoryResource extends LmpResource
{
    protected static ?string $model = PassengerCategory::class;

    protected static ?string $slug = 'passenger-categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'filament.resources.passenger_category';

    protected static ?string $pluralModelLabel = 'filament.resources.passenger_categories';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 20;

    protected static ?string $cluster = CuentasCluster::class;

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
        $main = array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($component) => $component->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $nameFields = $languages->map(function (Language $language) {
            return TextInput::make('translations.'.$language->id.'.name')
                ->label($language->display_name)
                ->maxLength(50);
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.passenger_category_tabs.general'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    TextInput::make('code')
                                        ->label(__('filament.resources.passenger_category_fields.code'))
                                        ->required()
                                        ->maxLength(30)
                                        ->alphaDash()
                                        ->unique(ignoreRecord: true),
                                ])
                                ->columns(2),
                        ]),
                    Tab::make(__('filament.resources.passenger_category_tabs.translations'))
                        ->schema([
                            Section::make('')
                                ->schema($nameFields)
                                ->columns(3),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.resources.passenger_category_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $translation) use ($search): void {
                            $translation->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('code')
                    ->label(__('filament.resources.passenger_category_columns.code'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('translations.language.locale'))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPassengerCategories::route('/'),
            'create' => Pages\CreatePassengerCategory::route('/create'),
            'edit' => Pages\EditPassengerCategory::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslations(PassengerCategory $record, array $translations, bool $forCreate): void
    {
        foreach ($translations as $languageId => $row) {
            $languageId = (int) $languageId;
            $name = isset($row['name']) ? trim((string) $row['name']) : '';

            if ($name === '') {
                if (! $forCreate) {
                    $record->translations()->where('language_id', $languageId)->delete();
                }

                continue;
            }

            if ($forCreate) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'name' => $name,
                ]);

                continue;
            }

            $record->translations()->updateOrCreate(
                ['language_id' => $languageId],
                ['name' => $name],
            );
        }
    }
}
