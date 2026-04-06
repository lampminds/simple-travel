<?php

namespace App\Filament\Pages;

use App\Models\ServiceFeature;
use App\Models\ServiceFeatureCategory;
use App\Models\ServiceFeatureScope;
use App\Models\ServiceType;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ManageServiceFeatureScopesPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'filament.resources.nav_services';

    protected static ?int $navigationSort = 15;

    protected static ?string $slug = 'manage-service-feature-scopes';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $firstTypeId = ServiceType::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->value('id');

        $this->form->fill([
            'service_type_id' => $firstTypeId,
            'category_ids' => [],
            'scoped_keep' => [],
            'unscoped_add' => [],
        ]);

        $this->loadScopeState();
    }

    protected function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('service_type_id')
                ->label(__('filament.resources.manage_service_feature_scopes.service_type'))
                ->options(
                    fn (): array => ServiceType::query()
                        ->where('active', true)
                        ->with(['translations.language.locale'])
                        ->ordered()
                        ->get()
                        ->mapWithKeys(fn (ServiceType $t) => [
                            $t->id => $t->name !== '' ? $t->name : $t->code,
                        ])
                        ->all()
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn () => $this->loadScopeState())
                ->required()
                ->columnSpanFull(),
            Section::make(__('filament.resources.manage_service_feature_scopes.section_categories'))
                ->description(__('filament.resources.manage_service_feature_scopes.help_categories'))
                ->schema([
                    CheckboxList::make('category_ids')
                        ->label(__('filament.resources.manage_service_feature_scopes.feature_categories'))
                        ->options(fn (): array => $this->getCategoryCheckboxOptions())
                        ->bulkToggleable()
                        ->columns([
                            'default' => 2,
                            'lg' => 4,
                        ])
                        ->live()
                        ->afterStateUpdated(fn () => $this->pruneFeatureSelectionsToCategories())
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
            Grid::make(2)->schema([
                Section::make(__('filament.resources.manage_service_feature_scopes.section_in_scope'))
                    ->description(__('filament.resources.manage_service_feature_scopes.help_in_scope'))
                    ->schema([
                        CheckboxList::make('scoped_keep')
                            ->label(__('filament.resources.manage_service_feature_scopes.in_scope'))
                            ->options(fn (): array => $this->getScopedKeepOptions())
                            ->bulkToggleable()
                            ->columns(2),
                    ]),
                Section::make(__('filament.resources.manage_service_feature_scopes.section_available'))
                    ->description(__('filament.resources.manage_service_feature_scopes.help_available'))
                    ->schema([
                        CheckboxList::make('unscoped_add')
                            ->label(__('filament.resources.manage_service_feature_scopes.available'))
                            ->options(fn (): array => $this->getUnscopedAddOptions())
                            ->bulkToggleable()
                            ->columns(2),
                    ]),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('manage-service-feature-scopes-form')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->sticky($this->areFormActionsSticky()),
                    ])
                    ->livewireSubmitHandler('save'),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament.resources.manage_service_feature_scopes.actions.save'))
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $serviceTypeId = (int) ($data['service_type_id'] ?? 0);
        if ($serviceTypeId < 1) {
            return;
        }

        $keep = array_map('intval', $data['scoped_keep'] ?? []);
        $add = array_map('intval', $data['unscoped_add'] ?? []);
        $final = array_values(array_unique(array_merge($keep, $add)));
        $final = $this->filterFeatureIdsToSelectedCategories($final, $this->getSelectedCategoryIdsFromData($data));

        $table = (new ServiceFeatureScope)->getTable();

        DB::transaction(function () use ($serviceTypeId, $final, $table): void {
            DB::table($table)->where('service_type_id', $serviceTypeId)->delete();
            $now = now();
            foreach ($final as $featureId) {
                if ($featureId < 1) {
                    continue;
                }
                DB::table($table)->insert([
                    'service_type_id' => $serviceTypeId,
                    'service_feature_id' => $featureId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        Notification::make()
            ->title(__('filament.resources.manage_service_feature_scopes.notifications.saved'))
            ->success()
            ->send();

        $this->loadScopeState();
    }

    protected function loadScopeState(): void
    {
        $serviceTypeId = $this->data['service_type_id'] ?? null;
        if (! $serviceTypeId) {
            $this->data['category_ids'] = [];
            $this->data['scoped_keep'] = [];
            $this->data['unscoped_add'] = [];

            return;
        }

        $this->data['category_ids'] = $this->getAllActiveCategoryIdsAsStrings();

        $table = (new ServiceFeatureScope)->getTable();

        $scopedIds = DB::table($table)
            ->where('service_type_id', $serviceTypeId)
            ->pluck('service_feature_id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        $this->data['scoped_keep'] = $scopedIds;
        $this->data['unscoped_add'] = [];
        $this->pruneFeatureSelectionsToCategories();
    }

    /**
     * Features currently in DB scope for the selected type (left column options).
     *
     * @return array<string, string>
     */
    protected function getScopedKeepOptions(): array
    {
        $serviceTypeId = $this->data['service_type_id'] ?? null;
        if (! $serviceTypeId) {
            return [];
        }

        $table = (new ServiceFeatureScope)->getTable();
        $ids = DB::table($table)
            ->where('service_type_id', $serviceTypeId)
            ->pluck('service_feature_id');

        return $this->optionsForFeatureIds($ids);
    }

    /**
     * Selectable features not yet in DB scope for the selected type (right column).
     *
     * @return array<string, string>
     */
    protected function getUnscopedAddOptions(): array
    {
        $serviceTypeId = $this->data['service_type_id'] ?? null;
        if (! $serviceTypeId) {
            return [];
        }

        $allowed = $this->getSelectedCategoryIds();
        if ($allowed === []) {
            return [];
        }

        $table = (new ServiceFeatureScope)->getTable();
        $scopedIds = DB::table($table)
            ->where('service_type_id', $serviceTypeId)
            ->pluck('service_feature_id')
            ->flip();

        $out = [];
        foreach ($this->getSelectableFeaturesInCategories($allowed) as $feature) {
            if ($scopedIds->has($feature->id)) {
                continue;
            }
            $out[(string) $feature->id] = $this->featureOptionLabel($feature);
        }

        return $out;
    }

    /**
     * @param  Collection<int, int>|iterable<int>  $ids
     * @return array<string, string>
     */
    protected function optionsForFeatureIds(iterable $ids): array
    {
        $idSet = collect($ids)->map(fn ($id) => (int) $id)->filter()->unique();
        if ($idSet->isEmpty()) {
            return [];
        }

        $allowed = $this->getSelectedCategoryIds();
        if ($allowed === []) {
            return [];
        }

        $features = $this->getSelectableFeaturesInCategories($allowed)->whereIn('id', $idSet->all());
        $out = [];
        foreach ($features as $feature) {
            $out[(string) $feature->id] = $this->featureOptionLabel($feature);
        }

        return $out;
    }

    protected function getSelectableFeatures(): Collection
    {
        return ServiceFeature::query()
            ->where('active', true)
            ->where('is_selectable', true)
            ->with(['serviceFeatureCategory.translations.language.locale', 'translations.language.locale'])
            ->ordered()
            ->get();
    }

    /**
     * Selectable features limited to the given category ids.
     *
     * @param  array<int>  $categoryIds
     */
    protected function getSelectableFeaturesInCategories(array $categoryIds): Collection
    {
        if ($categoryIds === []) {
            return collect();
        }

        return ServiceFeature::query()
            ->where('active', true)
            ->where('is_selectable', true)
            ->whereIn('service_feature_category_id', $categoryIds)
            ->with(['serviceFeatureCategory.translations.language.locale', 'translations.language.locale'])
            ->ordered()
            ->get();
    }

    /**
     * @return array<string, string>
     */
    protected function getCategoryCheckboxOptions(): array
    {
        return ServiceFeatureCategory::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->ordered()
            ->get()
            ->mapWithKeys(fn (ServiceFeatureCategory $c) => [
                (string) $c->id => $c->name !== '' ? $c->name : $c->code,
            ])
            ->all();
    }

    /**
     * @return array<string>
     */
    protected function getAllActiveCategoryIdsAsStrings(): array
    {
        return ServiceFeatureCategory::query()
            ->where('active', true)
            ->ordered()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * @return array<int>
     */
    protected function getSelectedCategoryIds(): array
    {
        return $this->getSelectedCategoryIdsFromData($this->data ?? []);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int>
     */
    protected function getSelectedCategoryIdsFromData(array $data): array
    {
        $raw = $data['category_ids'] ?? [];

        return collect(is_array($raw) ? $raw : [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int>  $featureIds
     * @param  array<int>  $categoryIds
     * @return array<int>
     */
    protected function filterFeatureIdsToSelectedCategories(array $featureIds, array $categoryIds): array
    {
        if ($featureIds === [] || $categoryIds === []) {
            return [];
        }

        return ServiceFeature::query()
            ->whereIn('id', $featureIds)
            ->where('is_selectable', true)
            ->whereIn('service_feature_category_id', $categoryIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Drops scoped_keep / unscoped_add entries for features whose category is not selected.
     */
    protected function pruneFeatureSelectionsToCategories(): void
    {
        $allowed = $this->getSelectedCategoryIds();
        if ($allowed === []) {
            $this->data['scoped_keep'] = [];
            $this->data['unscoped_add'] = [];

            return;
        }

        $validIds = $this->getSelectableFeaturesInCategories($allowed)->pluck('id')->flip();

        $this->data['scoped_keep'] = collect($this->data['scoped_keep'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $validIds->has($id))
            ->map(fn (int $id) => (string) $id)
            ->values()
            ->all();

        $this->data['unscoped_add'] = collect($this->data['unscoped_add'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $validIds->has($id))
            ->map(fn (int $id) => (string) $id)
            ->values()
            ->all();
    }

    protected function featureOptionLabel(ServiceFeature $feature): string
    {
        $cat = $feature->serviceFeatureCategory?->name ?? '';
        $name = $feature->name !== '' ? $feature->name : $feature->code;

        return $cat !== '' ? "{$cat} — {$name}" : $name;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.manage_service_feature_scopes.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament.resources.manage_service_feature_scopes.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }
}
