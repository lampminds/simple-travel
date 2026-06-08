<?php

namespace App\Livewire\Catalog;

use App\Models\Service;
use App\Support\ServiceWizardSkipsVariantsStep;
use App\Support\ServiceWizardStepEight;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProviderServicesTable extends Component
{
    /** @var Collection<int, Service> */
    public Collection $services;

    public function mount(Collection $services): void
    {
        $this->services = $services;
    }

    public function requestCopy(int $serviceId): void
    {
        $this->dispatch('open-copy-provider-service-modal', serviceId: $serviceId);
    }

    public function skipsVariantsForType(?string $code): bool
    {
        return ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($code);
    }

    public function hasAdvancedStepForType(?string $code): bool
    {
        return ServiceWizardStepEight::isEnabledForServiceTypeCode($code);
    }

    public function render(): View
    {
        return view('livewire.catalog.provider-services-table');
    }
}
