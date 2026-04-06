<?php

namespace App\Filament\Resources\PlanResource\RelationManagers;

use App\Models\Language;
use App\Models\PlanItem;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Filament\Resources\RelationManagers\RelationManager;

class PlanItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    protected static ?string $modelLabel = 'filament.resources.plan_item';

    protected static ?string $pluralModelLabel = 'filament.resources.plan_items';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return (string) __(static::$pluralModelLabel);
    }

    public function form(Schema $schema): Schema
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();
        $plan = $this->getOwnerRecord();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    Textarea::make("translations.{$lang->id}.text")
                        ->label(__('filament.resources.plan_item_fields.text'))
                        ->rows(2),
                ])
                ->collapsible();
        })->all();

        $rootItems = $plan->allItems()->whereNull('parent_id')->orderBy('sort_order')->get();

        $parentOptions = [null => '— ' . __('filament.resources.plan_item_fields.parent_id') . ' (raíz)'];
        foreach ($rootItems as $item) {
            $item->load('translations.language.locale');
            $parentOptions[$item->id] = $item->display_text ?: "Item #{$item->id}";
        }

        return $this->defaultForm($schema->schema([
            Section::make(__('filament.resources.plan_tabs.general'))
                ->schema([
                    Select::make('parent_id')
                        ->label(__('filament.resources.plan_item_fields.parent_id'))
                        ->options($parentOptions)
                        ->nullable(),
                    Toggle::make('active')
                        ->label(__('filament.resources.plan_item_fields.active'))
                        ->default(true),
                ])
                ->columns(2),
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.plan_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ]));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->columns([
                TextColumn::make('parent.display_text')
                    ->label(__('filament.resources.plan_item_columns.parent'))
                    ->placeholder('—')
                    ->limit(30),
                TextColumn::make('display_text')
                    ->label(__('filament.resources.plan_item_columns.text'))
                    ->limit(50)
                    ->wrap(),
                IconColumn::make('active')
                    ->label(__('filament.resources.plan_item_columns.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament-actions::create.single.modal.actions.create.label'))
                    ->using(function (array $data): Model {
                        $translations = $data['translations'] ?? [];
                        $data = Arr::except($data, ['translations']);
                        $data['plan_id'] = $this->getOwnerRecord()->id;
                        $record = $this->getRelationship()->create($data);
                        $this->syncItemTranslations($record, $translations);

                        return $record;
                    }),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()
                        ->fillForm(function (Model $record): array {
                            $record->load('translations');
                            $data = $record->attributesToArray();
                            $data['translations'] = [];
                            $languages = Language::query()->orderBy('id')->get();
                            foreach ($languages as $lang) {
                                $trans = $record->translations->firstWhere('language_id', $lang->id);
                                $data['translations'][$lang->id] = ['text' => $trans?->text ?? ''];
                            }

                            return $data;
                        })
                        ->using(function (array $data, Model $record): void {
                            $translations = $data['translations'] ?? [];
                            $data = Arr::except($data, ['translations']);
                            $record->update($data);
                            $this->syncItemTranslations($record, $translations);
                        }),
                    DeleteAction::make(),
                ]),
                RecordActionsPosition::BeforeColumns
            )
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale', 'parent.translations']));
    }

    protected function syncItemTranslations(PlanItem $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            $text = $row['text'] ?? '';
            if ($text !== '' && $text !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'text' => $text,
                ]);
            }
        }
    }
}
