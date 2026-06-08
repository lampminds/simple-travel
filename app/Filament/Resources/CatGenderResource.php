<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\SystemTablesCluster;
use App\Filament\Resources\CatGenderResource\Pages;
use App\Models\CatGender;
use App\Models\Language;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CatGenderResource extends BaseResource
{
    protected static ?string $model = CatGender::class;

    protected static ?string $cluster = SystemTablesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $modelLabel = 'filament.resources.cat_gender';

    protected static ?string $pluralModelLabel = 'filament.resources.cat_genders';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?int $navigationSort = 17;

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

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.cat_gender_fields.name'))
                        ->maxLength(255),
                ])
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.cat_gender_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.cat_gender_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['alpha_dash'])
                                    ->helperText(__('filament.resources.cat_gender_fields.code_help')),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.cat_gender_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.cat_gender_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('raw_code')
                    ->label(__('filament.resources.cat_gender_columns.code'))
                    ->getStateUsing(fn (CatGender $record): string => (string) $record->getRawOriginal('code'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->where('code', 'like', '%'.$search.'%');
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('code', $direction)),
                TextColumn::make('name')
                    ->label(__('filament.resources.cat_gender_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('translations_count')
                    ->label(__('filament.resources.cat_gender_columns.translations_count'))
                    ->counts('translations')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('active')
                    ->label(__('filament.resources.cat_gender_filter.active_status'))
                    ->options([
                        '1' => __('filament.resources.cat_gender_filter.active_only'),
                        '0' => __('filament.resources.cat_gender_filter.inactive_only'),
                    ])
                    ->placeholder(__('filament.resources.cat_gender_filter.active_all'))
                    ->query(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;
                        if ($value === null || $value === '') {
                            return;
                        }
                        $query->where('active', (bool) (int) $value);
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'translations.language.locale',
            ]))
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
            'index' => Pages\ListCatGenders::route('/'),
            'create' => Pages\CreateCatGender::route('/create'),
            'edit' => Pages\EditCatGender::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslationsFromForm(CatGender $record, array $translations, bool $forCreate): void
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

            $payload = ['name' => $name];

            if ($forCreate) {
                $payload['language_id'] = $languageId;
                $record->translations()->create($payload);
            } else {
                $record->translations()->updateOrCreate(
                    ['language_id' => $languageId],
                    $payload
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function assertUniqueCode(array $data, ?int $ignoreId = null): void
    {
        $query = CatGender::query()
            ->where('code', (string) ($data['code'] ?? ''));

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'code' => __('validation.unique', ['attribute' => __('filament.resources.cat_gender_fields.code')]),
            ]);
        }
    }
}
