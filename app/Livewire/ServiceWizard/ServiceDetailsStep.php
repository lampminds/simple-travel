<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Language;
use App\Models\Locale;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceDetailTopic;
use App\Models\ServiceDetailTopicCategory;
use App\Services\Translation\TranslationService;
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
    public int $serviceId;

    public int $serviceTypeId;

    /**
     * Each line is one logical detail (all languages share sort_order and active).
     *
     * @var list<array{sort_order: int, topic_id: int|null, category_id: int|null, active: bool, translations: array<int, array{description: string}>}>
     */
    public array $lines = [];

    public bool $showModal = false;

    /** null = adding; int = editing that index in $lines */
    public ?int $modalLineIndex = null;

    /**
     * Working copy for the modal form.
     *
     * @var array{sort_order?: int, topic_id: int|null, category_id: int|null, active: bool, translations: array<int, array{description: string}>}
     */
    public array $modalLine = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

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
    }

    public function openAddModal(): void
    {
        $this->resetValidation();
        $this->modalLineIndex = null;
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

        $result = app(TranslationService::class)->translateFromLanguage(
            sourceLanguageId: $sourceLanguageId,
            translationsPayload: $payload,
            userId: Auth::id()
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
            'modalLine.active' => ['boolean'],
        ];
        foreach ($languages as $lang) {
            $rules['modalLine.translations.'.$lang->id.'.description'] = ['nullable', 'string'];
        }

        $this->validate($rules, [], $this->modalValidationAttributes($languages));

        $this->assertTopicMatchesCategory($this->modalLine, 'modalLine');
        $this->assertModalHasText($languages);

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
        if ($index < 1 || ! isset($this->lines[$index])) {
            return;
        }
        $tmp = $this->lines[$index - 1];
        $this->lines[$index - 1] = $this->lines[$index];
        $this->lines[$index] = $tmp;
        $this->lines = array_values($this->lines);
        $this->renumberSortOrders();
        $this->persistAll();
    }

    public function moveLineDown(int $index): void
    {
        if (! isset($this->lines[$index + 1])) {
            return;
        }
        $tmp = $this->lines[$index + 1];
        $this->lines[$index + 1] = $this->lines[$index];
        $this->lines[$index] = $tmp;
        $this->lines = array_values($this->lines);
        $this->renumberSortOrders();
        $this->persistAll();
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

    protected function renumberSortOrders(): void
    {
        $order = 10;
        foreach (array_keys($this->lines) as $i) {
            $this->lines[$i]['sort_order'] = $order;
            $order += 10;
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

                foreach ($languages as $lang) {
                    $text = trim((string) data_get($row, 'translations.'.$lang->id.'.description'));
                    ServiceDetail::query()->create([
                        'service_id' => $service->id,
                        'service_detail_topic_id' => $topicId,
                        'language_id' => (int) $lang->id,
                        'description' => $text === '' ? null : $text,
                        'sort_order' => $sort,
                        'active' => $active,
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
            'active' => true,
            'translations' => $translations,
        ];
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
            'modalLine.translations' => __('wizard.step6_translations'),
        ];
        foreach ($languages as $lang) {
            $attrs['modalLine.translations.'.$lang->id.'.description'] = __('wizard.step6_description_for_locale', ['locale' => $lang->display_name]);
        }

        return $attrs;
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
            ->sortBy(fn (Language $language) => $language->display_name)
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
        ]);
    }
}
