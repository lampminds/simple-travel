<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiKnowledgeItemResource\Pages;
use App\Models\AiKnowledgeItem;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AiKnowledgeItemResource extends BaseResource
{
    protected static ?string $model = AiKnowledgeItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $modelLabel = 'filament.resources.ai_knowledge_item';

    protected static ?string $pluralModelLabel = 'filament.resources.ai_knowledge_items';

    protected static ?string $recordTitleAttribute = 'key';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_ai';

    protected static ?int $navigationSort = 1;

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
                    TextInput::make("translations.{$lang->id}.title")
                        ->label(__('filament.resources.ai_knowledge_fields.title'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.content_short")
                        ->label(__('filament.resources.ai_knowledge_fields.content_short'))
                        ->rows(2)
                        ->columnSpanFull(),
                    Textarea::make("translations.{$lang->id}.content")
                        ->label(__('filament.resources.ai_knowledge_fields.content'))
                        ->rows(8)
                        ->columnSpanFull(),
                    TextInput::make("translations.{$lang->id}.url")
                        ->label(__('filament.resources.ai_knowledge_fields.url'))
                        ->url()
                        ->maxLength(500),
                    TextInput::make("translations.{$lang->id}.tags")
                        ->label(__('filament.resources.ai_knowledge_fields.tags'))
                        ->helperText(__('filament.resources.ai_knowledge_fields.tags_help'))
                        ->maxLength(2000),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.ai_knowledge_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('key')
                                    ->label(__('filament.resources.ai_knowledge_fields.key'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['regex:/^[a-zA-Z0-9_]+$/'])
                                    ->helperText(__('filament.resources.ai_knowledge_fields.key_help')),
                                Toggle::make('is_active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.ai_knowledge_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.ai_knowledge_columns.id'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('key')
                    ->label(__('filament.resources.ai_knowledge_columns.key'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title_preview')
                    ->label(__('filament.resources.ai_knowledge_columns.title_preview'))
                    ->getStateUsing(function (AiKnowledgeItem $record): string {
                        $first = $record->translations->sortBy('language_id')->first();

                        return $first?->title ?? '—';
                    }),
                TextColumn::make('translations_count')
                    ->label(__('filament.resources.ai_knowledge_columns.translations_count'))
                    ->counts('translations')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['translations']))
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
            'index' => Pages\ListAiKnowledgeItems::route('/'),
            'create' => Pages\CreateAiKnowledgeItem::route('/create'),
            'edit' => Pages\EditAiKnowledgeItem::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslationsFromForm(AiKnowledgeItem $record, array $translations, bool $forCreate): void
    {
        foreach ($translations as $languageId => $row) {
            $languageId = (int) $languageId;
            $title = trim((string) ($row['title'] ?? ''));
            $content = trim((string) ($row['content'] ?? ''));

            if ($title === '' && $content === '') {
                if (! $forCreate) {
                    $record->translations()->where('language_id', $languageId)->delete();
                }

                continue;
            }

            $payload = [
                'title' => (string) ($row['title'] ?? ''),
                'content' => (string) ($row['content'] ?? ''),
                'content_short' => filled($row['content_short'] ?? null) ? (string) $row['content_short'] : null,
                'url' => filled($row['url'] ?? null) ? (string) $row['url'] : null,
                'tags' => static::normalizeTagsForStorage($row['tags'] ?? null),
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

    public static function normalizeTagsForStorage(mixed $tags): ?array
    {
        if ($tags === null || $tags === '') {
            return null;
        }

        if (is_array($tags)) {
            $out = array_values(array_filter(array_map(fn ($t) => trim((string) $t), $tags)));

            return $out === [] ? null : $out;
        }

        $out = array_values(array_filter(array_map(
            fn ($t) => trim((string) $t),
            explode(',', (string) $tags)
        )));

        return $out === [] ? null : $out;
    }
}
