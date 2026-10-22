<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceComponent;
use App\Models\ServiceVariant;
use App\Services\ServiceComponentService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Wizard step 4 (combo services only): manage the child variants that make
 * up each parent variant. Price stays on the parent variant; this step only
 * defines composition.
 */
class ServiceComponentsStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    public ?int $parentVariantId = null;

    /** @var array<string, mixed> */
    public array $form = [
        'child_variant_id' => '',
        'quantity' => '1',
        'day_number' => '',
        'inclusion_mode' => ServiceComponent::INCLUSION_INCLUDED,
        'notes' => '',
    ];

    public ?string $flashMessage = null;

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();
        $this->parentVariantId = $service->serviceVariants()->value('id');
    }

    public function updatedParentVariantId(): void
    {
        $this->flashMessage = null;
        $this->resetValidation();
    }

    public function addComponent(ServiceComponentService $components): void
    {
        $service = $this->authorizedService();

        $validated = $this->validate([
            'parentVariantId' => [
                'required',
                'integer',
                Rule::exists('service_variants', 'id')->where('service_id', $service->id),
            ],
            'form.child_variant_id' => ['required', 'integer', Rule::exists('service_variants', 'id')],
            'form.quantity' => ['required', 'integer', 'min:1'],
            'form.day_number' => ['nullable', 'integer', 'min:1'],
            'form.inclusion_mode' => [
                'required',
                Rule::in([
                    ServiceComponent::INCLUSION_INCLUDED,
                    ServiceComponent::INCLUSION_OPTIONAL,
                    ServiceComponent::INCLUSION_UPGRADE,
                ]),
            ],
            'form.notes' => ['nullable', 'string'],
        ], [], [
            'parentVariantId' => __('wizard.components_parent_variant'),
            'form.child_variant_id' => __('wizard.components_child_variant'),
            'form.quantity' => __('wizard.components_quantity'),
            'form.day_number' => __('wizard.components_day_number'),
            'form.inclusion_mode' => __('wizard.components_inclusion_mode'),
            'form.notes' => __('wizard.components_notes'),
        ]);

        $parentVariant = ServiceVariant::query()
            ->where('service_id', $service->id)
            ->with('service.serviceType')
            ->findOrFail((int) $validated['parentVariantId']);

        $childVariant = ServiceVariant::query()
            ->with('service.serviceType')
            ->findOrFail((int) $validated['form']['child_variant_id']);

        $components->addComponent($parentVariant, $childVariant, [
            'quantity' => (int) $validated['form']['quantity'],
            'day_number' => $validated['form']['day_number'] !== '' && $validated['form']['day_number'] !== null
                ? (int) $validated['form']['day_number']
                : null,
            'inclusion_mode' => (string) $validated['form']['inclusion_mode'],
            'notes' => $validated['form']['notes'] ?? null,
        ]);

        $this->form = [
            'child_variant_id' => '',
            'quantity' => '1',
            'day_number' => '',
            'inclusion_mode' => ServiceComponent::INCLUSION_INCLUDED,
            'notes' => '',
        ];
        $this->flashMessage = __('wizard.components_added');
    }

    public function removeComponent(int $componentId): void
    {
        $service = $this->authorizedService();

        ServiceComponent::query()
            ->whereKey($componentId)
            ->whereHas('parentVariant', fn ($query) => $query->where('service_id', $service->id))
            ->delete();

        $this->flashMessage = __('wizard.components_removed');
    }

    public function moveComponent(int $componentId, string $direction): void
    {
        $service = $this->authorizedService();

        $component = ServiceComponent::query()
            ->whereKey($componentId)
            ->whereHas('parentVariant', fn ($query) => $query->where('service_id', $service->id))
            ->first();

        if ($component === null) {
            return;
        }

        $siblings = ServiceComponent::query()
            ->where('parent_variant_id', $component->parent_variant_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $index = $siblings->search(fn (ServiceComponent $item): bool => (int) $item->id === (int) $component->id);
        if ($index === false) {
            return;
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapIndex < 0 || $swapIndex >= $siblings->count()) {
            return;
        }

        $other = $siblings[$swapIndex];
        $currentOrder = (int) $component->sort_order;
        $component->update(['sort_order' => (int) $other->sort_order]);
        $other->update(['sort_order' => $currentOrder]);
    }

    protected function authorizedService(): Service
    {
        $accountId = Auth::user()?->currentAccountId();
        abort_unless($accountId, 403);

        return Service::query()
            ->where('account_id', $accountId)
            ->where('service_type_id', $this->serviceTypeId)
            ->with('serviceType')
            ->findOrFail($this->serviceId);
    }

    public function render(): View
    {
        $components = app(ServiceComponentService::class);
        $service = $this->authorizedService();

        $parentVariants = $service->serviceVariants()
            ->with([
                'components.childVariant.service.translations.language.locale',
                'components.childVariant.translations.language.locale',
            ])
            ->get();

        if ($this->parentVariantId === null
            || $parentVariants->firstWhere('id', $this->parentVariantId) === null) {
            $this->parentVariantId = $parentVariants->first()?->id;
        }

        $selectedParent = $this->parentVariantId !== null
            ? $parentVariants->firstWhere('id', $this->parentVariantId)
            : null;

        return view('livewire.service-wizard.service-components-step', [
            'service' => $service,
            'parentVariants' => $parentVariants,
            'selectedParent' => $selectedParent,
            'selectedComponents' => $selectedParent?->components ?? collect(),
            'eligibleChildVariants' => $components->eligibleChildVariants($service),
        ]);
    }
}
