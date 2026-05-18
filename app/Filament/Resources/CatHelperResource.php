<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\CatHelperResource\Pages;
use App\Models\AccountType;
use App\Models\CatHelper;
use App\Models\Language;
use App\Models\ServiceType;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
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
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CatHelperResource extends BaseResource
{
    protected static ?string $model = CatHelper::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $modelLabel = 'filament.resources.cat_helper';

    protected static ?string $pluralModelLabel = 'filament.resources.cat_helpers';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

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
                    RichEditor::make("translations.{$lang->id}.text")
                        ->label(__('filament.resources.cat_helper_fields.text'))
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('cat-helpers/rich-text')
                        ->fileAttachmentsVisibility('public')
                        ->columnSpanFull()
                        ->helperText(__('filament.resources.cat_helper_fields.text_help')),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.cat_helper_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('screen_code')
                                    ->label(__('filament.resources.cat_helper_fields.screen_code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['alpha_dash'])
                                    ->helperText(__('filament.resources.cat_helper_fields.screen_code_help')),
                                TextInput::make('code')
                                    ->label(__('filament.resources.cat_helper_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['alpha_dash'])
                                    ->helperText(__('filament.resources.cat_helper_fields.code_help')),
                                Select::make('account_type_id')
                                    ->label(__('filament.resources.cat_helper_fields.account_type'))
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
                                Select::make('service_type_id')
                                    ->label(__('filament.resources.cat_helper_fields.service_type'))
                                    ->relationship(
                                        name: 'serviceType',
                                        titleAttribute: 'code',
                                        modifyQueryUsing: fn (Builder $query): Builder => $query
                                            ->where('active', true)
                                            ->orderBy('sort_order')
                                            ->orderBy('id'),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (ServiceType $record): string {
                                        $name = trim($record->name);

                                        return $name !== '' ? "{$name} ({$record->code})" : $record->code;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                                Textarea::make('notes')
                                    ->label(__('filament.resources.cat_helper_fields.notes'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.cat_helper_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.cat_helper_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('screen_code')
                    ->label(__('filament.resources.cat_helper_columns.screen_and_code'))
                    ->formatStateUsing(fn (CatHelper $record): string => $record->screen_code)
                    ->description(fn (CatHelper $record): string => $record->code)
                    ->searchable(query: function (Builder $query, string $search): void {
                        $term = '%'.$search.'%';
                        $query->where(function (Builder $q) use ($term): void {
                            $q->where('screen_code', 'like', $term)
                                ->orWhere('code', 'like', $term);
                        });
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query
                        ->orderBy('screen_code', $direction)
                        ->orderBy('code', $direction)),
                TextColumn::make('account_type_label')
                    ->label(__('filament.resources.cat_helper_columns.account_type'))
                    ->getStateUsing(fn (CatHelper $record): string => $record->accountType?->code ?? '—')
                    ->toggleable(),
                TextColumn::make('service_type_label')
                    ->label(__('filament.resources.cat_helper_columns.service_type'))
                    ->getStateUsing(fn (CatHelper $record): string => $record->serviceType?->code ?? '—')
                    ->toggleable(),
                TextColumn::make('text_preview')
                    ->label(__('filament.resources.cat_helper_columns.text_preview'))
                    ->wrap()
                    ->getStateUsing(function (CatHelper $record): string {
                        $first = $record->translations->sortBy('language_id')->first();

                        if ($first === null || $first->text === null || $first->text === '') {
                            return '—';
                        }

                        $plain = static::plainTextFirstWords($first->text, 10);

                        return $plain !== '' ? $plain : '—';
                    }),
                TextColumn::make('translations_count')
                    ->label(__('filament.resources.cat_helper_columns.translations_count'))
                    ->counts('translations')
                    ->sortable(),
            ])
            ->defaultSort('screen_code')
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'translations',
                'accountType',
                'serviceType',
            ]))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('duplicate')
                        ->label(__('filament.resources.cat_helper_duplicate'))
                        ->icon('heroicon-o-document-duplicate')
                        ->url(fn (CatHelper $record): string => static::getUrl('create').'?duplicate='.$record->getKey()),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    /**
     * Form state to pre-fill create when duplicating a helper (no DB write until save).
     *
     * @return array<string, mixed>
     */
    public static function duplicateFormDefaults(CatHelper $source): array
    {
        $source->loadMissing('translations');

        $translations = [];
        foreach (Language::query()->with('locale')->orderBy('id')->get() as $lang) {
            $trans = $source->translations->firstWhere('language_id', $lang->id);
            $translations[$lang->id] = [
                'text' => $trans?->text ?? '',
            ];
        }

        return [
            'screen_code' => $source->screen_code,
            'code' => static::nextCopyCode($source),
            'account_type_id' => $source->account_type_id,
            'service_type_id' => $source->service_type_id,
            'active' => (bool) $source->active,
            'notes' => $source->notes,
            'translations' => $translations,
        ];
    }

    /**
     * Unique helper key within the same screen / scope tuple when copying.
     */
    private static function nextCopyCode(CatHelper $source): string
    {
        $base = trim((string) $source->code);
        if ($base === '') {
            $base = 'helper';
        }

        $candidate = $base.'-copy';
        $counter = 2;

        while (CatHelper::query()
            ->where('screen_code', $source->screen_code)
            ->where('code', $candidate)
            ->where('service_type_id', $source->service_type_id)
            ->where('account_type_id', $source->account_type_id)
            ->exists()
        ) {
            $candidate = $base.'-copy-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatHelpers::route('/'),
            'create' => Pages\CreateCatHelper::route('/create'),
            'edit' => Pages\EditCatHelper::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslationsFromForm(CatHelper $record, array $translations, bool $forCreate): void
    {
        foreach ($translations as $languageId => $row) {
            $languageId = (int) $languageId;
            $html = isset($row['text']) ? (string) $row['text'] : '';

            if (static::isEffectivelyEmptyHtml($html)) {
                if (! $forCreate) {
                    $record->translations()->where('language_id', $languageId)->delete();
                }

                continue;
            }

            $payload = [
                'text' => $html,
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

    public static function isEffectivelyEmptyHtml(string $html): bool
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return true;
        }

        // Keep blocks that contain images even when there is no visible text.
        if (preg_match('/<img[\s>]/i', $trimmed)) {
            return false;
        }

        return trim(strip_tags($trimmed)) === '';
    }

    /**
     * Plain text from HTML, limited to the first N words (Unicode whitespace).
     * Tags are replaced with spaces before stripping so block/inline elements do not glue words.
     */
    private static function plainTextFirstWords(?string $html, int $wordLimit = 10): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $withSpaces = preg_replace('/<[^>]*>/u', ' ', $html);
        $plain = strip_tags($withSpaces);
        $plain = str_replace("\xc2\xa0", ' ', $plain);
        $plain = trim(preg_replace('/\s+/u', ' ', $plain));
        if ($plain === '') {
            return '';
        }

        $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($words === []) {
            return '';
        }

        $slice = array_slice($words, 0, $wordLimit);
        $out = implode(' ', $slice);
        if (count($words) > $wordLimit) {
            $out .= ' …';
        }

        return $out;
    }

    /**
     * Normalizes optional FK fields before persistence or duplicate checks.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeScopeFields(array $data): array
    {
        foreach (['service_type_id', 'account_type_id'] as $key) {
            if (! array_key_exists($key, $data) || $data[$key] === '' || $data[$key] === false) {
                $data[$key] = null;

                continue;
            }
            if (is_numeric($data[$key])) {
                $data[$key] = (int) $data[$key];
            }
        }

        return $data;
    }

    /**
     * Ensures no duplicate row for the same (screen_code, code, service_type_id, account_type_id) tuple.
     * Call {@see normalizeScopeFields()} on $data before this method.
     *
     * @param  array<string, mixed>  $data
     */
    public static function assertUniqueScope(array $data, ?int $ignoreId = null): void
    {
        $query = CatHelper::query()
            ->where('screen_code', (string) ($data['screen_code'] ?? ''))
            ->where('code', (string) ($data['code'] ?? ''))
            ->where('service_type_id', $data['service_type_id'] ?? null)
            ->where('account_type_id', $data['account_type_id'] ?? null);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'code' => __('validation.unique', ['attribute' => __('filament.resources.cat_helper_fields.code')]),
            ]);
        }
    }
}
