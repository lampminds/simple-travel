<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\CatFaqResource\Pages;
use App\Models\AccountType;
use App\Models\CatFaq;
use App\Models\Language;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class CatFaqResource extends BaseResource
{
    protected static ?string $model = CatFaq::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $modelLabel = 'filament.resources.cat_faq';

    protected static ?string $pluralModelLabel = 'filament.resources.cat_faqs';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?int $navigationSort = 15;

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
                    TextInput::make("translations.{$lang->id}.question")
                        ->label(__('filament.resources.cat_faq_fields.question'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.answer")
                        ->label(__('filament.resources.cat_faq_fields.answer'))
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.cat_faq_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.cat_faq_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['alpha_dash'])
                                    ->helperText(__('filament.resources.cat_faq_fields.code_help')),
                                Select::make('account_type_id')
                                    ->label(__('filament.resources.cat_faq_fields.account_type'))
                                    ->relationship(
                                        name: 'accountType',
                                        titleAttribute: 'code',
                                        modifyQueryUsing: fn (Builder $query): Builder => $query
                                            ->where('active', true)
                                            ->orderBy('sort_order')
                                            ->orderBy('id'),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (AccountType $record): string {
                                        $name = trim($record->name);

                                        return $name !== '' ? "{$name} ({$record->code})" : $record->code;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                TextInput::make('sort_order')
                                    ->label(__('filament.resources.cat_faq_fields.sort_order'))
                                    ->numeric()
                                    ->default(9999)
                                    ->required(),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                                Textarea::make('notes')
                                    ->label(__('filament.resources.cat_faq_fields.notes'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.cat_faq_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.cat_faq_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('filament.resources.cat_faq_columns.sort_order'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.cat_faq_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_type_label')
                    ->label(__('filament.resources.cat_faq_columns.account_type'))
                    ->getStateUsing(fn (CatFaq $record): string => $record->accountType?->code ?? '—')
                    ->toggleable(),
                TextColumn::make('question_preview')
                    ->label(__('filament.resources.cat_faq_columns.question_preview'))
                    ->wrap()
                    ->getStateUsing(fn (CatFaq $record): string => $record->question !== '' ? $record->question : '—'),
                TextColumn::make('translations_count')
                    ->label(__('filament.resources.cat_faq_columns.translations_count'))
                    ->counts('translations')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('account_type_id')
                    ->label(__('filament.resources.cat_faq_fields.account_type'))
                    ->relationship(
                        'accountType',
                        'code',
                        fn (Builder $query): Builder => $query->where('active', true)->orderBy('sort_order'),
                    )
                    ->columnSpan(2)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('active')
                    ->label(__('filament.resources.cat_faq_filter.active_status'))
                    ->options([
                        '1' => __('filament.resources.cat_faq_filter.active_only'),
                        '0' => __('filament.resources.cat_faq_filter.inactive_only'),
                    ])
                    ->placeholder(__('filament.resources.cat_faq_filter.active_all'))
                    ->query(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;
                        if ($value === null || $value === '') {
                            return;
                        }
                        $query->where('active', (bool) (int) $value);
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'translations',
                'accountType',
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
            'index' => Pages\ListCatFaqs::route('/'),
            'create' => Pages\CreateCatFaq::route('/create'),
            'edit' => Pages\EditCatFaq::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslationsFromForm(CatFaq $record, array $translations, bool $forCreate): void
    {
        foreach ($translations as $languageId => $row) {
            $languageId = (int) $languageId;
            $question = isset($row['question']) ? trim((string) $row['question']) : '';
            $answer = isset($row['answer']) ? trim((string) $row['answer']) : '';

            if ($question === '' && $answer === '') {
                if (! $forCreate) {
                    $record->translations()->where('language_id', $languageId)->delete();
                }

                continue;
            }

            $payload = [
                'question' => $question !== '' ? $question : null,
                'answer' => $answer !== '' ? $answer : null,
            ];

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
     * @return array<string, mixed>
     */
    public static function normalizeScopeFields(array $data): array
    {
        if (! array_key_exists('account_type_id', $data) || $data['account_type_id'] === '' || $data['account_type_id'] === false) {
            $data['account_type_id'] = null;
        } elseif (is_numeric($data['account_type_id'])) {
            $data['account_type_id'] = (int) $data['account_type_id'];
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function assertUniqueCodeAccountType(array $data, ?int $ignoreId = null): void
    {
        $data = static::normalizeScopeFields($data);

        $query = CatFaq::query()
            ->where('code', (string) ($data['code'] ?? ''))
            ->where('account_type_id', $data['account_type_id'] ?? null);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'code' => __('validation.unique', ['attribute' => __('filament.resources.cat_faq_fields.code')]),
            ]);
        }
    }
}
