<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Language;
use App\Models\Locale;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceDetailTopic;
use App\Models\ServiceDetailTopicCategory;
use App\Services\Translation\TranslationService;
use App\Support\AiUsageContext;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ServiceDetailsStep extends Component
{
    /** @var list<string> */
    public const VISIBILITY_TABS = ['public', 'operator', 'internal'];

    public int $serviceId;

    public int $serviceTypeId;

    /** @var array<string, string|null> */
    public array $catalogVisibilityTabHelpHtml = [];

    public ?string $catalogMandatoryHelpHtml = null;

    public string $activeVisibilityTab = 'public';

    /** Visibility context for the add/edit modal (public | operator | internal). */
    public string $modalVisibility = 'public';

    /**
     * Each line is one logical detail (all languages share sort_order and active).
     *
     * @var list<array{sort_order: int, topic_id: int|null, category_id: int|null, condition_key_id: int|null, is_mandatory: bool, active: bool, translations: array<int, array{description: string}>}>
     */
    public array $lines = [];

    public bool $showModal = false;

    /** null = adding; int = editing that index in $lines */
    public ?int $modalLineIndex = null;

    /**
     * Working copy for the modal form.
     *
     * @var array{sort_order?: int, topic_id: int|null, category_id: int|null, condition_key_id: int|null, is_mandatory: bool, active: bool, translations: array<int, array{description: string}>}
     */
    public array $modalLine = [];

    /**
     * @param  array<string, string|null>  $catalogVisibilityTabHelpHtml
     */
    public function mount(
        int $serviceId,
        int $serviceTypeId,
        array $catalogVisibilityTabHelpHtml = [],
        ?string $catalogMandatoryHelpHtml = null,
    ): void {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;
        $this->catalogVisibilityTabHelpHtml = $catalogVisibilityTabHelpHtml;
        $this->catalogMandatoryHelpHtml = $catalogMandatoryHelpHtml;

        $service = $this->authorizedService();
        $langIds = $this->wizardLanguages()->pluck('id')->map(fn ($id) => (int) $id)->all();

        $existing = ServiceDetail::query()
            ->where('service_id', $service->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($existing->isEmpty()) {
            $this->lines = [];
            $this->modalLine = $this->blankLine();

            return;
        }

        $buckets = [];
        foreach ($existing as $detail) {
            $tid = (int) $detail->service_detail_topic_id;
            $ord = (int) $detail->sort_order;
            $key = "{$ord}_{$tid}";
            if (! isset($buckets[$key])) {
                $topic = ServiceDetailTopic::query()->find($tid);
                $buckets[$key] = [
                    'sort_order' => $ord,
                    'topic_id' => $tid,
                    'category_id' => $topic !== null ? (int) $topic->service_detail_topic_category_id : null,
                    'condition_key_id' => $detail->condition_key_id !== null
                        ? (int) $detail->condition_key_id
                        : $this->conditionKeyIdForTopic($topic),
                    'is_mandatory' => (bool) $detail->is_mandatory,
                    'visibility' => $this->normalizeVisibility($topic?->visibility),
                    'active' => (bool) $detail->active,
                    'translations' => [],
                ];
                foreach ($langIds as $lid) {
                    $buckets[$key]['translations'][$lid] = ['description' => ''];
                }
            }
            $lid = (int) $detail->language_id;
            if (! isset($buckets[$key]['translations'][$lid])) {
                $buckets[$key]['translations'][$lid] = ['description' => ''];
            }
            $buckets[$key]['translations'][$lid]['description'] = (string) ($detail->description ?? '');
        }

        $this->lines = collect($buckets)
            ->sortBy(fn (array $b): int => (int) ($b['sort_order'] ?? 0))
            ->values()
            ->all();

        $this->modalLine = $this->blankLine();
        $this->modalVisibility = $this->activeVisibilityTab;
    }

    public function setVisibilityTab(string $visibility): void
    {
        if (! in_array($visibility, self::VISIBILITY_TABS, true)) {
            return;
        }

        $this->activeVisibilityTab = $visibility;
        if ($this->showModal) {
            $this->closeModal();
        }
    }

    public function openAddModal(): void
    {
        $this->resetValidation();
        $this->modalLineIndex = null;
        $this->modalVisibility = $this->activeVisibilityTab;
        $this->modalLine = $this->blankLine();
        $this->showModal = true;
    }

    public function openEditModal(int $index): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }
        $this->resetValidation();
        $this->modalLineIndex = $index;
        $this->modalVisibility = $this->lineVisibility($this->lines[$index]);
        $this->activeVisibilityTab = $this->modalVisibility;
        $this->modalLine = $this->duplicateLine($this->lines[$index]);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->modalLineIndex = null;
        $this->modalLine = $this->blankLine();
        $this->resetValidation();
    }

    public function clearModalTopic(): void
    {
        $this->modalLine['topic_id'] = null;
    }

    public function translateModal(int $sourceLanguageId): void
    {
        $payload = [];
        foreach ($this->wizardLanguages() as $lang) {
            $id = (int) $lang->id;
            $payload[$id] = [
                'name' => '',
                'description' => (string) data_get($this->modalLine, "translations.$id.description", ''),
            ];
        }

        $user = Auth::user();
        $account = $user?->currentAccount();

        $result = app(TranslationService::class)->translateFromLanguage(
            sourceLanguageId: $sourceLanguageId,
            translationsPayload: $payload,
            userId: $user?->id,
            usageContext: $user !== null
                ? new AiUsageContext(
                    userId: (int) $user->id,
                    accountId: $user->currentAccountId(),
                    accountTypeId: $account?->account_type_id !== null ? (int) $account->account_type_id : null,
                    source: 'service_wizard.livewire.details',
                )
                : null,
        );

        if (! $result['ok']) {
            throw ValidationException::withMessages([
                'modalLine.translations' => $result['message'],
            ]);
        }

        foreach ($result['translations'] as $langId => $data) {
            $id = (int) $langId;
            if (! isset($this->modalLine['translations'][$id])) {
                $this->modalLine['translations'][$id] = ['description' => ''];
            }
            $desc = trim((string) ($data['description'] ?? ''));
            if ($desc !== '') {
                $this->modalLine['translations'][$id]['description'] = $desc;
            }
        }
    }

    public function saveModal(): void
    {
        $languages = $this->wizardLanguages();

        $rules = [
            'modalLine.topic_id' => ['required', 'integer', Rule::exists('cat_service_detail_topics', 'id')],
            'modalLine.category_id' => ['required', 'integer', Rule::exists('cat_service_detail_topic_categories', 'id')],
            'modalLine.is_mandatory' => ['boolean'],
            'modalLine.active' => ['boolean'],
        ];
        foreach ($languages as $lang) {
            $rules['modalLine.translations.'.$lang->id.'.description'] = ['nullable', 'string'];
        }

        $this->validate($rules, [], $this->modalValidationAttributes($languages));

        $this->assertTopicMatchesCategory($this->modalLine, 'modalLine');
        $this->assertTopicMatchesVisibility($this->modalLine, 'modalLine');
        $this->assertModalHasText($languages);

        $topic = ServiceDetailTopic::query()->find((int) $this->modalLine['topic_id']);
        $this->modalLine['visibility'] = $this->normalizeVisibility($topic?->visibility);
        $this->modalLine['condition_key_id'] = $this->conditionKeyIdForTopic($topic);

        if ($this->modalLineIndex === null) {
            $this->lines[] = $this->modalLine;
        } else {
            if (! isset($this->lines[$this->modalLineIndex])) {
                $this->closeModal();

                return;
            }
            $this->lines[$this->modalLineIndex] = $this->modalLine;
        }

        $this->lines = array_values($this->lines);
        $this->renumberSortOrders();
        $this->persistAll();
        $this->closeModal();
    }

    public function moveLineUp(int $index): void
    {
        $this->moveLineWithinVisibility($index, -1);
    }

    public function moveLineDown(int $index): void
    {
        $this->moveLineWithinVisibility($index, 1);
    }

    public function toggleLineActive(int $index): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }
        $this->lines[$index]['active'] = ! ($this->lines[$index]['active'] ?? true);
        $this->persistAll();
    }

    public function deleteLine(int $index): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        $this->renumberSortOrders();
        $this->persistAll();
    }

    /**
     * @param  Collection<int, Language>  $languages
     */
    protected function assertModalHasText(Collection $languages): void
    {
        foreach ($languages as $lang) {
            if (trim((string) data_get($this->modalLine, 'translations.'.$lang->id.'.description')) !== '') {
                return;
            }
        }
        throw ValidationException::withMessages([
            'modalLine.translations' => __('wizard.step6_at_least_one_description'),
        ]);
    }

    /**
     * @param  array{topic_id?: int|null, category_id?: int|null}  $row
     */
    protected function assertTopicMatchesCategory(array $row, string $prefix): void
    {
        $tid = (int) ($row['topic_id'] ?? 0);
        $cid = isset($row['category_id']) ? (int) $row['category_id'] : 0;
        if ($tid < 1) {
            return;
        }
        $topic = ServiceDetailTopic::query()->find($tid);
        if ($topic === null) {
            return;
        }
        if ($cid > 0 && (int) $topic->service_detail_topic_category_id !== $cid) {
            throw ValidationException::withMessages([
                $prefix.'.topic_id' => __('wizard.step6_topic_category_mismatch'),
            ]);
        }
    }

    /**
     * @param  array{topic_id?: int|null}  $row
     */
    protected function assertTopicMatchesVisibility(array $row, string $prefix): void
    {
        $tid = (int) ($row['topic_id'] ?? 0);
        if ($tid < 1) {
            return;
        }
        $topic = ServiceDetailTopic::query()->find($tid);
        if ($topic === null) {
            return;
        }
        if ($this->normalizeVisibility($topic->visibility) !== $this->modalVisibility) {
            throw ValidationException::withMessages([
                $prefix.'.topic_id' => __('wizard.step6_topic_visibility_mismatch'),
            ]);
        }
    }

    protected function moveLineWithinVisibility(int $index, int $direction): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }

        $visibility = $this->lineVisibility($this->lines[$index]);
        $swapIndex = null;

        if ($direction < 0) {
            for ($i = $index - 1; $i >= 0; $i--) {
                if ($this->lineVisibility($this->lines[$i]) === $visibility) {
                    $swapIndex = $i;
                    break;
                }
            }
        } else {
            for ($i = $index + 1, $count = count($this->lines); $i < $count; $i++) {
                if ($this->lineVisibility($this->lines[$i]) === $visibility) {
                    $swapIndex = $i;
                    break;
                }
            }
        }

        if ($swapIndex === null) {
            return;
        }

        $tmp = $this->lines[$swapIndex];
        $this->lines[$swapIndex] = $this->lines[$index];
        $this->lines[$index] = $tmp;
        $this->lines = array_values($this->lines);
        $this->renumberSortOrders();
        $this->persistAll();
    }

    protected function lineVisibility(array $line): string
    {
        if (isset($line['visibility']) && is_string($line['visibility'])) {
            return $this->normalizeVisibility($line['visibility']);
        }

        return $this->normalizeVisibility(
            ServiceDetailTopic::query()->find((int) ($line['topic_id'] ?? 0))?->visibility
        );
    }

    protected function normalizeVisibility(?string $visibility): string
    {
        return in_array($visibility, self::VISIBILITY_TABS, true) ? $visibility : 'public';
    }

    public function lineMatchesActiveTab(array $line): bool
    {
        return $this->lineVisibility($line) === $this->activeVisibilityTab;
    }

    public function canMoveLineUp(int $index): bool
    {
        if (! isset($this->lines[$index])) {
            return false;
        }
        $visibility = $this->lineVisibility($this->lines[$index]);
        for ($i = $index - 1; $i >= 0; $i--) {
            if ($this->lineVisibility($this->lines[$i]) === $visibility) {
                return true;
            }
        }

        return false;
    }

    public function canMoveLineDown(int $index): bool
    {
        if (! isset($this->lines[$index])) {
            return false;
        }
        $visibility = $this->lineVisibility($this->lines[$index]);
        $count = count($this->lines);
        for ($i = $index + 1; $i < $count; $i++) {
            if ($this->lineVisibility($this->lines[$i]) === $visibility) {
                return true;
            }
        }

        return false;
    }

    protected function renumberSortOrders(): void
    {
        $orderByVisibility = array_fill_keys(self::VISIBILITY_TABS, 10);
        foreach (array_keys($this->lines) as $i) {
            $visibility = $this->lineVisibility($this->lines[$i]);
            $this->lines[$i]['sort_order'] = $orderByVisibility[$visibility];
            $orderByVisibility[$visibility] += 10;
        }
    }

    protected function persistAll(): void
    {
        $service = $this->authorizedService();
        $languages = $this->wizardLanguages();

        $this->renumberSortOrders();

        DB::transaction(function () use ($service, $languages): void {
            ServiceDetail::query()->where('service_id', $service->id)->delete();

            foreach ($this->lines as $row) {
                $topicId = (int) ($row['topic_id'] ?? 0);
                if ($topicId < 1) {
                    continue;
                }
                $hasText = false;
                foreach ($languages as $lang) {
                    if (trim((string) data_get($row, 'translations.'.$lang->id.'.description')) !== '') {
                        $hasText = true;
                        break;
                    }
                }
                if (! $hasText) {
                    continue;
                }

                $sort = (int) ($row['sort_order'] ?? 9999);
                $active = (bool) ($row['active'] ?? true);
                $topic = ServiceDetailTopic::query()->find($topicId);
                $conditionKeyId = $this->conditionKeyIdForTopic($topic);
                $isMandatory = (bool) ($row['is_mandatory'] ?? false);

                foreach ($languages as $lang) {
                    $text = trim((string) data_get($row, 'translations.'.$lang->id.'.description'));
                    ServiceDetail::query()->create([
                        'service_id' => $service->id,
                        'service_detail_topic_id' => $topicId,
                        'language_id' => (int) $lang->id,
                        'description' => $text === '' ? null : $text,
                        'sort_order' => $sort,
                        'active' => $active,
                        'is_mandatory' => $isMandatory,
                        'condition_key_id' => $conditionKeyId > 0 ? $conditionKeyId : null,
                    ]);
                }
            }
        });

        session()->flash('status', __('wizard.step6_saved'));
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array<string, mixed>
     */
    protected function duplicateLine(array $line): array
    {
        $translations = [];
        foreach ($line['translations'] ?? [] as $lid => $t) {
            $translations[(int) $lid] = ['description' => (string) ($t['description'] ?? '')];
        }
        foreach ($this->wizardLanguages() as $lang) {
            $id = (int) $lang->id;
            if (! isset($translations[$id])) {
                $translations[$id] = ['description' => ''];
            }
        }

        return [
            'sort_order' => (int) ($line['sort_order'] ?? 0),
            'topic_id' => isset($line['topic_id']) ? (int) $line['topic_id'] : null,
            'category_id' => isset($line['category_id']) ? (int) $line['category_id'] : null,
            'condition_key_id' => isset($line['condition_key_id']) ? (int) $line['condition_key_id'] : null,
            'is_mandatory' => (bool) ($line['is_mandatory'] ?? false),
            'visibility' => $this->lineVisibility($line),
            'active' => (bool) ($line['active'] ?? true),
            'translations' => $translations,
        ];
    }

    /**
     * @return array{sort_order: int, topic_id: int|null, category_id: int|null, active: bool, translations: array<int, array{description: string}>}
     */
    protected function blankLine(): array
    {
        $translations = [];
        foreach ($this->wizardLanguages() as $lang) {
            $translations[(int) $lang->id] = ['description' => ''];
        }

        return [
            'sort_order' => 0,
            'topic_id' => null,
            'category_id' => null,
            'condition_key_id' => null,
            'is_mandatory' => false,
            'visibility' => $this->modalVisibility,
            'active' => true,
            'translations' => $translations,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ServiceDetailTopicCategory>  $categories
     * @param  \Illuminate\Support\Collection<int|string, \Illuminate\Support\Collection<int, ServiceDetailTopic>>  $topicsByCategory
     * @return \Illuminate\Support\Collection<int, ServiceDetailTopicCategory>
     */
    public function categoriesForVisibility(Collection $categories, Collection $topicsByCategory, string $visibility): Collection
    {
        $visibility = $this->normalizeVisibility($visibility);

        return $categories->filter(function (ServiceDetailTopicCategory $cat) use ($topicsByCategory, $visibility): bool {
            $topics = $topicsByCategory->get((int) $cat->id, collect());

            return $topics->contains(
                fn (ServiceDetailTopic $topic): bool => $this->normalizeVisibility($topic->visibility) === $visibility
            );
        })->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ServiceDetailTopic>  $topics
     * @return \Illuminate\Support\Collection<int, ServiceDetailTopic>
     */
    public function topicsForCategoryAndVisibility(Collection $topics, string $visibility): Collection
    {
        $visibility = $this->normalizeVisibility($visibility);

        return $topics
            ->filter(fn (ServiceDetailTopic $topic): bool => $this->normalizeVisibility($topic->visibility) === $visibility)
            ->values();
    }

    public function hasCatalogForVisibility(string $visibility): bool
    {
        return ServiceDetailTopic::query()
            ->where('active', true)
            ->where('visibility', $this->normalizeVisibility($visibility))
            ->exists();
    }

    /**
     * @param  Collection<int, Language>  $languages
     * @return array<string, string>
     */
    protected function modalValidationAttributes(Collection $languages): array
    {
        $attrs = [
            'modalLine.topic_id' => __('wizard.step6_topic'),
            'modalLine.category_id' => __('wizard.step6_category'),
            'modalLine.is_mandatory' => __('wizard.step6_is_mandatory'),
            'modalLine.translations' => __('wizard.step6_translations'),
        ];
        foreach ($languages as $lang) {
            $attrs['modalLine.translations.'.$lang->id.'.description'] = __('wizard.step6_description_for_locale', ['locale' => $lang->display_name]);
        }

        return $attrs;
    }

    protected function conditionKeyIdForTopic(?ServiceDetailTopic $topic): ?int
    {
        if ($topic === null || $topic->condition_key_id === null) {
            return null;
        }

        return (int) $topic->condition_key_id;
    }

    public function excerptForLine(array $line): string
    {
        $locale = app()->getLocale();
        foreach ($this->wizardLanguages() as $lang) {
            $lang->loadMissing('locale');
            if ($lang->locale !== null && Locale::primaryTagMatches($lang->locale, $locale)) {
                $text = trim((string) data_get($line, 'translations.'.$lang->id.'.description', ''));

                return $text === '' ? '—' : Str::limit($text, 80);
            }
        }
        foreach ($this->wizardLanguages() as $lang) {
            $text = trim((string) data_get($line, 'translations.'.$lang->id.'.description', ''));
            if ($text !== '') {
                return Str::limit($text, 80);
            }
        }

        return '—';
    }

    /**
     * @return Collection<int, Language>
     */
    protected function wizardLanguages(): Collection
    {
        return Language::query()
            ->with('locale')
            ->get()
            ->values();
    }

    protected function authorizedService(): Service
    {
        $accountId = Auth::user()?->currentAccountId();
        abort_unless($accountId, 403);

        return Service::query()
            ->where('account_id', $accountId)
            ->where('service_type_id', $this->serviceTypeId)
            ->findOrFail($this->serviceId);
    }

    public function render(): View
    {
        $categories = ServiceDetailTopicCategory::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $topics = ServiceDetailTopic::query()
            ->where('active', true)
            ->with(['translations.language.locale'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (ServiceDetailTopic $t) => (int) $t->service_detail_topic_category_id);

        $topicIds = collect($this->lines)->pluck('topic_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $topicsById = $topicIds->isEmpty()
            ? collect()
            : ServiceDetailTopic::query()
                ->whereIn('id', $topicIds->all())
                ->with(['translations.language.locale'])
                ->get()
                ->keyBy('id');

        return view('livewire.service-wizard.service-details-step', [
            'categories' => $categories,
            'topicsByCategory' => $topics,
            'topicsById' => $topicsById,
            'languages' => $this->wizardLanguages(),
            'visibilityTabs' => self::VISIBILITY_TABS,
            'modalCategories' => $this->categoriesForVisibility($categories, $topics, $this->modalVisibility),
        ]);
    }
}
