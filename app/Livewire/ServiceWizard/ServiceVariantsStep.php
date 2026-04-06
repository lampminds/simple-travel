<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Currency;
use App\Models\Service;
use App\Models\ServiceVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceVariantsStep extends Component
{
    public const MODE_LIST = 'list';

    public const MODE_FORM = 'form';

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

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;
        $this->mode = self::MODE_LIST;
        $this->form = [];
        $this->editingVariantId = null;
        $this->isCopy = false;
    }

    public function startCreate(): void
    {
        $this->clearFlash();
        $this->mode = self::MODE_FORM;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->form = $this->defaultVariantRow();
    }

    public function startEdit(int $variantId): void
    {
        $this->clearFlash();
        $service = $this->authorizedService();
        $variant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($variantId)
            ->firstOrFail();

        $this->mode = self::MODE_FORM;
        $this->editingVariantId = $variant->id;
        $this->isCopy = false;
        $this->form = $this->variantToRow($variant);
    }

    /**
     * Preload create form with a duplicate of an existing variant (new SKU suggested, no id).
     */
    public function copyFrom(int $variantId): void
    {
        $this->clearFlash();
        $service = $this->authorizedService();
        $variant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->whereKey($variantId)
            ->firstOrFail();

        $row = $this->variantToRow($variant);
        unset($row['id']);
        $row['sku'] = $this->suggestedUniqueSku($variant->sku);

        $this->mode = self::MODE_FORM;
        $this->editingVariantId = null;
        $this->isCopy = true;
        $this->form = $row;
    }

    /**
     * Build a SKU that does not collide with existing variants for this service.
     */
    protected function suggestedUniqueSku(string $originalSku): string
    {
        $service = $this->authorizedService();
        $counter = 0;

        while ($counter < 1000) {
            $suffix = $counter === 0 ? '-copy' : '-copy-' . $counter;
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

    public function cancel(): void
    {
        $this->clearFlash();
        $this->mode = self::MODE_LIST;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->form = [];
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
    }

    public function save(): void
    {
        $this->clearFlash();
        $service = $this->authorizedService();

        $validator = Validator::make(
            ['form' => $this->form],
            $this->validationRulesForSingle($service),
            [],
            ['form.sku' => 'SKU']
        );

        $validator->validate();

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

        DB::transaction(function () use ($service, $payload): void {
            $id = $this->editingVariantId;

            if ($id) {
                $variant = ServiceVariant::query()
                    ->where('service_id', $service->id)
                    ->whereKey($id)
                    ->firstOrFail();
                $variant->update($payload);
            } else {
                ServiceVariant::query()->create($payload);
            }
        });

        $this->flashMessage = __('wizard.variants_saved');
        $this->mode = self::MODE_LIST;
        $this->editingVariantId = null;
        $this->isCopy = false;
        $this->form = [];
    }

    protected function clearFlash(): void
    {
        $this->flashMessage = null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function variantToRow(ServiceVariant $v): array
    {
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
        $statuses = ['active', 'inactive', 'hidden'];
        $pricing = ['per_person', 'per_unit', 'per_room', 'per_vehicle', 'per_group'];
        $inventory = ['unlimited', 'per_day', 'per_timeslot', 'per_departure'];

        $ignoreId = $this->editingVariantId;

        return [
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
        ];
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
            ->with('currency.lmpCurrency')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function render(): View
    {
        $currencies = Currency::query()
            ->with('lmpCurrency')
            ->orderBy('id')
            ->get();

        return view('livewire.service-wizard.service-variants-step', [
            'currencies' => $currencies,
            'variants' => $this->variantsForList(),
        ]);
    }
}
