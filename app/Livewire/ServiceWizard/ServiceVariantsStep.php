<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\PriceFormatService;
use App\Services\Translation\TranslationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ServiceVariantsStep extends Component
{
    use WithFileUploads;

    public const MODE_LIST = 'list';

    public int $serviceId;

    public int $serviceTypeId;

    public string $mode = self::MODE_LIST;

    /** When editing an existing variant; null when creating a new one. */
    public ?int $editingVariantId = null;

    /** Single-variant form (create or edit). */
    /** @var array<string, mixed> */
    public array $form = [];

    public ?string $flashMessage = null;

    public bool $isCopy = false;

    /** True while the variant form (create / edit / copy) is shown in a modal over the list. */
    public bool $showVariantFormModal = false;

    /** Bootstrap tab id: general | pricing | descriptions | images */
    public string $variantFormTab = 'general';

    /**
     * Tabs that had validation errors on the last failed save (for nav highlighting).
     *
     * @var list<string>
     */
    public array $variantTabsWithErrors = [];

    /** @var mixed */
    public $mainImage = null;

    /** @var array<int, mixed> */
    public array $galleryImages = [];

    /**
     * Catalog helper HTML keyed by variant field (see {@see ServiceWizardVariantCatalogHelpers::FORM_FIELD_KEYS}).
     *
     * @var array<string, string|null>
     */
    public array $catalogVariantFieldHelpHtml = [];

    /** Snapshot when the variant modal opens; used to detect unsaved edits. */
    public array $variantFormSnapshot = [];

    public bool $showDiscardConfirm = false;

    /**
     * Action to run after the user confirms discarding unsaved changes.
     *
     * @var array{action: string, variantId?: int}|null
     */
    public ?array $pendingVariantNavigation = null;

    public function mount(int $serviceId, int $serviceTypeId, array $catalogVariantFieldHelpHtml = []): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;
        $this->catalogVariantFieldHelpHtml = $catalogVariantFieldHelpHtml;
        $this->mode = self::MODE_LIST;
        $this->form = [];
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->variantFormTab = 'general';
        $this->variantTabsWithErrors = [];
        $this->showVariantFormModal = false;
        $this->resetImageUploads();
    }

    public function updatedMainImage(): void
    {
        $this->resetValidation('mainImage');
    }

    public function updatedGalleryImages(): void
    {
        $this->resetValidation('galleryImages');
    }

    public function startCreate(): void
    {
        $this->clearFlash();
        $this->resetValidation();
        $this->mode = self::MODE_LIST;
        $this->showVariantFormModal = true;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->variantFormTab = 'general';
        $this->variantTabsWithErrors = [];
        $this->resetImageUploads();
        $this->form = $this->defaultVariantRow();
        $this->captureVariantFormSnapshot();
    }

    public function requestStartCreate(): void
    {
        if ($this->showVariantFormModal && $this->hasUnsavedVariantChanges()) {
            $this->pendingVariantNavigation = ['action' => 'create'];
            $this->showDiscardConfirm = true;

            return;
        }

        $this->startCreate();
    }

    public function startEdit(int $variantId): void
    {
        $this->clearFlash();
        $this->resetValidation();
        $service = $this->authorizedService();
        $variant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($variantId)
            ->with(['translations', 'media'])
            ->firstOrFail();

        $this->mode = self::MODE_LIST;
        $this->showVariantFormModal = true;
        $this->editingVariantId = $variant->id;
        $this->isCopy = false;
        $this->variantFormTab = 'general';
        $this->variantTabsWithErrors = [];
        $this->resetImageUploads();
        $this->form = $this->variantToRow($variant);
        $this->captureVariantFormSnapshot();
    }

    public function requestStartEdit(int $variantId): void
    {
        if ($this->showVariantFormModal && $this->hasUnsavedVariantChanges()) {
            $this->pendingVariantNavigation = ['action' => 'edit', 'variantId' => $variantId];
            $this->showDiscardConfirm = true;

            return;
        }

        $this->startEdit($variantId);
    }

    /**
     * Preload create form with a duplicate of an existing variant (new SKU suggested, no id).
     */
    public function copyFrom(int $variantId): void
    {
        $this->clearFlash();
        $this->resetValidation();
        $service = $this->authorizedService();
        $variant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($variantId)
            ->with('translations')
            ->firstOrFail();

        $row = $this->variantToRow($variant);
        unset($row['id']);
        $row['sku'] = $this->suggestedUniqueSku($variant->sku);

        $this->mode = self::MODE_LIST;
        $this->showVariantFormModal = true;
        $this->editingVariantId = null;
        $this->isCopy = true;
        $this->variantFormTab = 'general';
        $this->variantTabsWithErrors = [];
        $this->resetImageUploads();
        $this->form = $row;
        $this->captureVariantFormSnapshot();
    }

    public function requestCopyFrom(int $variantId): void
    {
        if ($this->showVariantFormModal && $this->hasUnsavedVariantChanges()) {
            $this->pendingVariantNavigation = ['action' => 'copy', 'variantId' => $variantId];
            $this->showDiscardConfirm = true;

            return;
        }

        $this->copyFrom($variantId);
    }

    /**
     * Build a SKU that does not collide with existing variants for this service.
     */
    protected function suggestedUniqueSku(string $originalSku): string
    {
        $service = $this->authorizedService();
        $counter = 0;

        while ($counter < 1000) {
            $suffix = $counter === 0 ? '-copy' : '-copy-'.$counter;
            $candidate = $originalSku.$suffix;
            if (mb_strlen($candidate) > 255) {
                $candidate = mb_substr($originalSku, 0, max(0, 255 - mb_strlen($suffix))).$suffix;
            }

            $exists = ServiceVariant::query()
                ->where('service_id', $service->id)
                ->where('sku', $candidate)
                ->exists();

            if (! $exists) {
                return $candidate;
            }

            $counter++;
        }

        return mb_substr($originalSku, 0, 200).'-copy-'.uniqid();
    }

    public function requestCancel(): void
    {
        if ($this->showVariantFormModal && $this->hasUnsavedVariantChanges()) {
            $this->pendingVariantNavigation = ['action' => 'cancel'];
            $this->showDiscardConfirm = true;

            return;
        }

        $this->cancel();
    }

    public function dismissDiscardConfirm(): void
    {
        $this->showDiscardConfirm = false;
        $this->pendingVariantNavigation = null;
    }

    public function confirmDiscard(): void
    {
        $pending = $this->pendingVariantNavigation;
        $this->showDiscardConfirm = false;
        $this->pendingVariantNavigation = null;

        if ($pending === null) {
            $this->cancel();

            return;
        }

        match ($pending['action']) {
            'cancel' => $this->cancel(),
            'create' => $this->startCreate(),
            'edit' => $this->startEdit((int) ($pending['variantId'] ?? 0)),
            'copy' => $this->copyFrom((int) ($pending['variantId'] ?? 0)),
            default => $this->cancel(),
        };
    }

    public function cancel(): void
    {
        $this->clearFlash();
        $this->resetValidation();
        $this->mode = self::MODE_LIST;
        $this->showVariantFormModal = false;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->form = [];
        $this->variantFormTab = 'general';
        $this->variantTabsWithErrors = [];
        $this->variantFormSnapshot = [];
        $this->showDiscardConfirm = false;
        $this->pendingVariantNavigation = null;
        $this->resetImageUploads();
    }

    public function hasUnsavedVariantChanges(): bool
    {
        if (! $this->showVariantFormModal || $this->variantFormSnapshot === []) {
            return false;
        }

        return json_encode($this->variantFormSnapshot) !== json_encode($this->currentVariantFormState());
    }

    public function deleteVariant(int $variantId): void
    {
        $service = $this->authorizedService();

        ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($variantId)
            ->delete();

        $this->flashMessage = __('wizard.variants_deleted');
        $this->mode = self::MODE_LIST;
        $this->showVariantFormModal = false;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->form = [];
        $this->variantFormTab = 'general';
        $this->variantTabsWithErrors = [];
        $this->resetImageUploads();
    }

    public function removeVariantMainImage(): void
    {
        if (! $this->editingVariantId) {
            return;
        }

        $service = $this->authorizedService();
        $variant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($this->editingVariantId)
            ->firstOrFail();

        $variant->clearMediaCollection(ServiceVariant::MEDIA_COLLECTION_MAIN);
        $this->flashMessage = __('wizard.variant_media_main_removed');
    }

    public function removeVariantGalleryMedia(int $mediaId): void
    {
        if (! $this->editingVariantId) {
            return;
        }

        $service = $this->authorizedService();
        $variant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($this->editingVariantId)
            ->firstOrFail();

        $media = $variant
            ->media()
            ->where('collection_name', ServiceVariant::MEDIA_COLLECTION_GALLERY)
            ->whereKey($mediaId)
            ->first();

        if ($media !== null) {
            $media->delete();
            $this->flashMessage = __('wizard.variant_media_gallery_removed');
        }
    }

    public function translateDescriptions(int $sourceLanguageId): void
    {
        $payload = [];
        foreach ($this->wizardLanguages() as $lang) {
            $id = (int) $lang->id;
            $payload[$id] = [
                'name' => (string) data_get($this->form, "translations.$id.name", ''),
                'description' => (string) data_get($this->form, "translations.$id.description", ''),
            ];
        }

        $result = app(TranslationService::class)->translateFromLanguage(
            sourceLanguageId: $sourceLanguageId,
            translationsPayload: $payload,
            userId: Auth::id()
        );

        if (! $result['ok']) {
            throw ValidationException::withMessages([
                'form.translations' => $result['message'],
            ]);
        }

        foreach ($result['translations'] as $langId => $data) {
            $id = (int) $langId;
            if (! isset($this->form['translations'][$id])) {
                $this->form['translations'][$id] = ['name' => '', 'description' => ''];
            }
            $name = trim((string) ($data['name'] ?? ''));
            if ($name !== '') {
                $this->form['translations'][$id]['name'] = $name;
            }
            $description = trim((string) ($data['description'] ?? ''));
            if ($description !== '') {
                $this->form['translations'][$id]['description'] = $description;
            }
        }
    }

    public function save(): void
    {
        $this->clearFlash();
        $this->normalizeFormBeforeValidation();
        $service = $this->authorizedService();

        $languages = $this->wizardLanguages();

        $validator = Validator::make(
            [
                'form' => $this->form,
                'mainImage' => $this->mainImage,
                'galleryImages' => $this->galleryImages,
            ],
            array_merge(
                $this->validationRulesForSingle($service),
                [
                    'mainImage' => ['nullable', 'image', 'max:'.ServiceVariant::MEDIA_MAX_FILE_SIZE_KB],
                    'galleryImages' => ['nullable', 'array'],
                    'galleryImages.*' => ['image', 'max:'.ServiceVariant::MEDIA_MAX_FILE_SIZE_KB],
                ]
            ),
            [],
            $this->validationAttributeNames($languages)
        );

        if ($validator->fails()) {
            $errorKeys = $validator->errors()->keys();
            $this->variantTabsWithErrors = array_values(array_unique(array_map(
                fn (string $key): string => $this->mapValidationKeyToTab($key),
                $errorKeys
            )));
            $this->variantFormTab = $this->firstTabForErrorKeys($errorKeys);
            throw new ValidationException($validator);
        }

        $this->variantTabsWithErrors = [];

        $row = $this->form;

        if ($this->editingVariantId) {
            $existing = ServiceVariant::query()
                ->where('service_id', $service->id)
                ->whereKey($this->editingVariantId)
                ->firstOrFail();
            $sortOrder = (int) ($existing->sort_order ?? 9999);
        } else {
            $sortOrder = $this->nextSortOrderForService((int) $service->id);
        }

        $payload = $this->rowToPayload((int) $service->id, $row, $sortOrder);

        $mainImage = $this->mainImage;
        $galleryImages = $this->galleryImages;

        DB::transaction(function () use ($service, $payload, $row, $mainImage, $galleryImages): void {
            $id = $this->editingVariantId;

            if ($id) {
                $variant = ServiceVariant::query()
                    ->where('service_id', $service->id)
                    ->whereKey($id)
                    ->firstOrFail();
                $variant->update($payload);
            } else {
                $variant = ServiceVariant::query()->create($payload);
            }

            $this->syncVariantTranslations($variant, $row);

            if ($mainImage !== null) {
                $variant
                    ->addMedia($mainImage)
                    ->toMediaCollection(ServiceVariant::MEDIA_COLLECTION_MAIN);
            }

            foreach ($galleryImages as $file) {
                $variant
                    ->addMedia($file)
                    ->toMediaCollection(ServiceVariant::MEDIA_COLLECTION_GALLERY);
            }
        });

        $this->resetImageUploads();
        $this->resetValidation();

        $this->flashMessage = __('wizard.variants_saved');
        $this->mode = self::MODE_LIST;
        $this->showVariantFormModal = false;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->form = [];
        $this->variantFormTab = 'general';
        $this->variantFormSnapshot = [];
    }

    protected function captureVariantFormSnapshot(): void
    {
        $this->variantFormSnapshot = $this->currentVariantFormState();
    }

    /**
     * @return array{form: array<string, mixed>, mainImage: bool, galleryImages: bool}
     */
    protected function currentVariantFormState(): array
    {
        return [
            'form' => $this->normalizeFormForComparison($this->form),
            'mainImage' => $this->mainImage !== null,
            'galleryImages' => count(array_filter($this->galleryImages)) > 0,
        ];
    }

    /**
     * @param  array<string, mixed>  $form
     * @return array<string, mixed>
     */
    protected function normalizeFormForComparison(array $form): array
    {
        $normalized = [];

        foreach ($form as $key => $value) {
            if ($key === 'id') {
                continue;
            }

            if ($key === 'translations' && is_array($value)) {
                $translations = [];
                foreach ($value as $langId => $data) {
                    if (! is_array($data)) {
                        continue;
                    }
                    $translations[(int) $langId] = [
                        'name' => trim((string) ($data['name'] ?? '')),
                        'description' => trim((string) ($data['description'] ?? '')),
                    ];
                }
                ksort($translations);
                $normalized['translations'] = $translations;

                continue;
            }

            if (is_string($value)) {
                $normalized[$key] = trim($value);

                continue;
            }

            $normalized[$key] = $value;
        }

        ksort($normalized);

        return $normalized;
    }

    protected function resetImageUploads(): void
    {
        $this->mainImage = null;
        $this->galleryImages = [];
    }

    protected function syncVariantTranslations(ServiceVariant $variant, array $row): void
    {
        $translations = $row['translations'] ?? [];
        foreach (Language::query()->get(['id']) as $language) {
            $langId = (int) $language->id;
            $data = $translations[$langId] ?? $translations[(string) $langId] ?? [];
            $variant->translations()->updateOrCreate(
                ['language_id' => $langId],
                [
                    'name' => trim((string) ($data['name'] ?? '')),
                    'description' => isset($data['description']) && $data['description'] !== ''
                        ? (string) $data['description']
                        : null,
                ]
            );
        }
    }

    protected function clearFlash(): void
    {
        $this->flashMessage = null;
    }

    /**
     * Apply defaults for select fields that may not sync when their tab is hidden in the DOM.
     */
    protected function normalizeFormBeforeValidation(): void
    {
        $defaults = $this->defaultVariantRow();

        foreach (['status', 'pricing_type', 'inventory_type'] as $key) {
            $value = $this->form[$key] ?? null;
            if ($value === null || $value === '') {
                $this->form[$key] = $defaults[$key];
            }
        }

        if (($this->form['currency_id'] ?? '') === '' && ($defaults['currency_id'] ?? '') !== '') {
            $this->form['currency_id'] = $defaults['currency_id'];
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function variantToRow(ServiceVariant $v): array
    {
        $v->loadMissing('translations');

        $translations = [];
        foreach (Language::query()->get(['id']) as $language) {
            $langId = (int) $language->id;
            $t = $v->translations->firstWhere('language_id', $langId);
            $translations[$langId] = [
                'name' => $t->name ?? '',
                'description' => $t->description ?? '',
            ];
        }

        return [
            'id' => $v->id,
            'sku' => $v->sku,
            'status' => $v->status,
            'pricing_type' => $v->pricing_type,
            'base_price' => (string) $v->base_price,
            'currency_id' => (string) $v->currency_id,
            'inventory_type' => $v->inventory_type,
            'inventory_total' => $v->inventory_total !== null ? (string) $v->inventory_total : '',
            'capacity_min' => $v->capacity_min !== null ? (string) $v->capacity_min : '',
            'capacity_max' => $v->capacity_max !== null ? (string) $v->capacity_max : '',
            'min_advance_booking_hours' => $v->min_advance_booking_hours !== null ? (string) $v->min_advance_booking_hours : '',
            'max_advance_booking_days' => $v->max_advance_booking_days !== null ? (string) $v->max_advance_booking_days : '',
            'start_time' => $this->formatTimeForInput($v->start_time),
            'end_time' => $this->formatTimeForInput($v->end_time),
            'translations' => $translations,
        ];
    }

    protected function formatTimeForInput(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_string($value) && preg_match('/^\d{2}:\d{2}/', $value)) {
            return substr($value, 0, 5);
        }
        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format('H:i');
        }

        return '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultVariantRow(): array
    {
        $currencyId = Currency::query()->orderBy('id')->value('id');

        $translations = [];
        foreach (Language::query()->get(['id']) as $language) {
            $translations[(int) $language->id] = [
                'name' => '',
                'description' => '',
            ];
        }

        return [
            'sku' => '',
            'status' => 'active',
            'pricing_type' => 'per_person',
            'base_price' => '0.00',
            'currency_id' => $currencyId !== null ? (string) $currencyId : '',
            'inventory_type' => 'unlimited',
            'inventory_total' => '',
            'capacity_min' => '',
            'capacity_max' => '',
            'min_advance_booking_hours' => '',
            'max_advance_booking_days' => '',
            'start_time' => '',
            'end_time' => '',
            'translations' => $translations,
        ];
    }

    protected function nextSortOrderForService(int $serviceId): int
    {
        return (int) (ServiceVariant::query()
            ->where('service_id', $serviceId)
            ->max('sort_order') ?? 0) + 10;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function rowToPayload(int $serviceId, array $row, int $sortOrder): array
    {
        $inventoryType = $row['inventory_type'] ?? '';
        $inventoryTotal = null;
        if ($inventoryType !== 'unlimited') {
            $inventoryTotal = $row['inventory_total'] === '' || $row['inventory_total'] === null
                ? null
                : (int) $row['inventory_total'];
        }

        return [
            'service_id' => $serviceId,
            'sku' => trim((string) $row['sku']),
            'status' => $row['status'],
            'pricing_type' => $row['pricing_type'],
            'base_price' => $row['base_price'],
            'currency_id' => (int) $row['currency_id'],
            'inventory_type' => $row['inventory_type'],
            'inventory_total' => $inventoryTotal,
            'capacity_min' => $row['capacity_min'] === '' || $row['capacity_min'] === null ? null : (int) $row['capacity_min'],
            'capacity_max' => $row['capacity_max'] === '' || $row['capacity_max'] === null ? null : (int) $row['capacity_max'],
            'min_advance_booking_hours' => $row['min_advance_booking_hours'] === '' || $row['min_advance_booking_hours'] === null
                ? null
                : (int) $row['min_advance_booking_hours'],
            'max_advance_booking_days' => $row['max_advance_booking_days'] === '' || $row['max_advance_booking_days'] === null
                ? null
                : (int) $row['max_advance_booking_days'],
            'start_time' => $row['start_time'] === '' || $row['start_time'] === null ? null : $row['start_time'],
            'end_time' => $row['end_time'] === '' || $row['end_time'] === null ? null : $row['end_time'],
            'sort_order' => $sortOrder,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function validationRulesForSingle(Service $service): array
    {
        $statuses = ['active', 'suspended', 'discontinued'];
        $pricing = ['per_person', 'per_unit', 'per_room', 'per_vehicle', 'per_group'];
        $inventory = ['unlimited', 'per_day', 'per_timeslot', 'per_departure'];

        $ignoreId = $this->editingVariantId;

        $rules = [
            'form.sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_variants', 'sku')
                    ->where(fn ($q) => $q->where('service_id', $service->id))
                    ->ignore($ignoreId),
            ],
            'form.status' => ['required', Rule::in($statuses)],
            'form.pricing_type' => ['required', Rule::in($pricing)],
            'form.base_price' => ['required', 'numeric', 'min:0'],
            'form.currency_id' => ['required', 'integer', 'exists:cat_currencies,id'],
            'form.inventory_type' => ['required', Rule::in($inventory)],
            'form.inventory_total' => ['nullable', 'integer', 'min:0'],
            'form.capacity_min' => ['nullable', 'integer', 'min:0'],
            'form.capacity_max' => ['nullable', 'integer', 'min:0'],
            'form.min_advance_booking_hours' => ['nullable', 'integer', 'min:0'],
            'form.max_advance_booking_days' => ['nullable', 'integer', 'min:0'],
            'form.start_time' => ['nullable', 'date_format:H:i'],
            'form.end_time' => ['nullable', 'date_format:H:i'],
            'form.translations' => ['required', 'array'],
        ];

        foreach (Language::query()->get(['id']) as $language) {
            $id = $language->id;
            $rules["form.translations.{$id}.name"] = ['required', 'string', 'max:255'];
            $rules["form.translations.{$id}.description"] = ['nullable', 'string'];
        }

        return $rules;
    }

    /**
     * Human-readable :attribute names for validation messages.
     *
     * @return array<string, string>
     */
    protected function validationAttributeNames(Collection $languages): array
    {
        $f = 'filament.resources.service_variant_fields';

        $attrs = [
            'form.sku' => __($f.'.sku'),
            'form.status' => __($f.'.status'),
            'form.pricing_type' => __($f.'.pricing_type'),
            'form.base_price' => __($f.'.base_price'),
            'form.currency_id' => __($f.'.currency'),
            'form.inventory_type' => __($f.'.inventory_type'),
            'form.inventory_total' => __($f.'.inventory_total'),
            'form.capacity_min' => __($f.'.capacity_min'),
            'form.capacity_max' => __($f.'.capacity_max'),
            'form.min_advance_booking_hours' => __($f.'.min_advance_booking_hours'),
            'form.max_advance_booking_days' => __($f.'.max_advance_booking_days'),
            'form.start_time' => __($f.'.start_time'),
            'form.end_time' => __($f.'.end_time'),
            'form.translations' => __('wizard.variants_tab_descriptions'),
            'mainImage' => __('wizard.variant_media_main_heading'),
            'galleryImages' => __('wizard.variant_media_gallery_heading'),
            'galleryImages.*' => __('wizard.variant_media_gallery_image'),
        ];

        foreach ($languages as $language) {
            $id = (int) $language->id;
            $label = $language->display_name;
            $attrs["form.translations.{$id}.name"] = __('wizard.variants_translation_name_for_locale', ['locale' => $label]);
            $attrs["form.translations.{$id}.description"] = __('wizard.variants_translation_description_for_locale', ['locale' => $label]);
        }

        return $attrs;
    }

    /**
     * Map a dot-notation validation key to a form tab id.
     */
    protected function mapValidationKeyToTab(string $key): string
    {
        if (str_starts_with($key, 'form.translations')) {
            return 'descriptions';
        }
        if ($key === 'mainImage' || str_starts_with($key, 'galleryImages')) {
            return 'images';
        }
        if (in_array($key, ['form.pricing_type', 'form.base_price', 'form.currency_id'], true)) {
            return 'pricing';
        }

        return 'general';
    }

    /**
     * First tab (fixed order) that contains at least one validation error.
     *
     * @param  list<string>  $errorKeys
     */
    protected function firstTabForErrorKeys(array $errorKeys): string
    {
        $order = ['general', 'pricing', 'descriptions', 'images'];
        $seen = [];
        foreach ($errorKeys as $key) {
            $seen[$this->mapValidationKeyToTab($key)] = true;
        }
        foreach ($order as $tab) {
            if (! empty($seen[$tab])) {
                return $tab;
            }
        }

        return 'general';
    }

    public function variantTabHasError(string $tab): bool
    {
        return in_array($tab, $this->variantTabsWithErrors, true);
    }

    public function formatVariantBasePrice(ServiceVariant $variant): string
    {
        $accountId = Auth::user()?->currentAccountId();

        return app(PriceFormatService::class)->formatWithCurrency(
            $variant->base_price,
            $variant->currency,
            accountId: $accountId !== null ? (int) $accountId : null,
        );
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

    protected function variantsForList(): Collection
    {
        $service = $this->authorizedService();

        return ServiceVariant::query()
            ->where('service_id', $service->id)
            ->with(['currency.lmpCurrency', 'translations.language.locale', 'media'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Languages for translation fields (sorted like step 1).
     *
     * @return Collection<int, Language>
     */
    protected function wizardLanguages(): Collection
    {
        return Language::query()
            ->with('locale')
            ->get()
            ->values();
    }

    public function render(): View
    {
        $currencies = Currency::query()
            ->with('lmpCurrency')
            ->orderBy('id')
            ->get();

        $languages = $this->wizardLanguages();

        $mainMedia = null;
        $galleryMedia = collect();
        if ($this->showVariantFormModal && $this->editingVariantId) {
            $variant = ServiceVariant::query()
                ->where('service_id', $this->authorizedService()->id)
                ->whereKey($this->editingVariantId)
                ->with('media')
                ->first();
            if ($variant) {
                $mainMedia = $variant->getFirstMedia(ServiceVariant::MEDIA_COLLECTION_MAIN);
                $galleryMedia = $variant->getMedia(ServiceVariant::MEDIA_COLLECTION_GALLERY);
            }
        }

        return view('livewire.service-wizard.service-variants-step', [
            'currencies' => $currencies,
            'variants' => $this->variantsForList(),
            'languages' => $languages,
            'variantMainMedia' => $mainMedia,
            'variantGalleryMedia' => $galleryMedia,
        ]);
    }
}
