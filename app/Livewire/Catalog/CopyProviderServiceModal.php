<?php

namespace App\Livewire\Catalog;

use App\Models\Service;
use App\Services\ServiceCopyService;
use App\Support\ServiceCopyOptions;
use App\Support\ServiceCopySections;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class CopyProviderServiceModal extends Component
{
    public bool $showCopyModal = false;

    public ?int $copySourceServiceId = null;

    public string $copySourceLabel = '';

    public ?string $copyServiceTypeCode = null;

    /** @var array<string, bool> */
    public array $copySections = [];

    #[On('open-copy-provider-service-modal')]
    public function open(int $serviceId): void
    {
        $service = $this->findAuthorizedService($serviceId);
        $service->loadMissing(['translations', 'serviceType']);

        $code = (string) ($service->serviceType?->code ?? '');
        $this->copySourceServiceId = $service->id;
        $this->copySourceLabel = $service->name !== '' ? $service->name : '#'.$service->id;
        $this->copyServiceTypeCode = $code;

        $this->copySections = [];
        foreach (ServiceCopyOptions::defaultSectionsForServiceTypeCode($code) as $section) {
            $this->copySections[$section] = true;
        }

        $this->showCopyModal = true;
    }

    public function closeCopyModal(): void
    {
        $this->showCopyModal = false;
        $this->copySourceServiceId = null;
        $this->copySourceLabel = '';
        $this->copyServiceTypeCode = null;
        $this->copySections = [];
        $this->resetValidation();
    }

    public function selectAllCopySections(): void
    {
        foreach (ServiceCopySections::forServiceTypeCode($this->copyServiceTypeCode) as $section) {
            $this->copySections[$section] = true;
        }
    }

    public function selectNoCopySections(): void
    {
        foreach (ServiceCopySections::forServiceTypeCode($this->copyServiceTypeCode) as $section) {
            $this->copySections[$section] = false;
        }
    }

    public function performCopy(): void
    {
        if ($this->copySourceServiceId === null) {
            return;
        }

        $options = ServiceCopyOptions::fromChecked($this->copySections, $this->copyServiceTypeCode);

        if ($options->sections === []) {
            throw ValidationException::withMessages([
                'copySections' => __('wizard.service_copy_sections_required'),
            ]);
        }

        $source = $this->findAuthorizedService($this->copySourceServiceId);
        $source->loadMissing('serviceType');

        $newService = app(ServiceCopyService::class)->copy($source, $options);

        $serviceTypeCode = (string) ($newService->serviceType?->code ?? $this->copyServiceTypeCode ?? '');

        session()->flash('status', __('wizard.service_copy_success'));

        $this->redirectRoute('services.wizard.step1.edit', [
            'serviceType' => $serviceTypeCode,
            'service' => $newService,
        ], navigate: false);
    }

    /**
     * @return list<string>
     */
    public function availableCopySections(): array
    {
        return ServiceCopySections::forServiceTypeCode($this->copyServiceTypeCode);
    }

    public function copySectionLabel(string $section): string
    {
        if ($section === ServiceCopySections::ADVANCED && $this->copyServiceTypeCode === 'transfer') {
            return __('wizard.provider_services_action_step8_transfer');
        }

        return match ($section) {
            ServiceCopySections::BASE => __('wizard.provider_services_action_step1'),
            ServiceCopySections::STATUS => __('wizard.provider_services_action_step2'),
            ServiceCopySections::FEATURES => __('wizard.provider_services_action_step3'),
            ServiceCopySections::VARIANTS => __('wizard.provider_services_action_step4'),
            ServiceCopySections::IMAGES => __('wizard.provider_services_action_step5'),
            ServiceCopySections::DETAILS => __('wizard.provider_services_action_step6'),
            ServiceCopySections::EXPERIENCES => __('wizard.provider_services_action_step7'),
            ServiceCopySections::ADVANCED => __('wizard.provider_services_action_step8'),
            default => $section,
        };
    }

    protected function findAuthorizedService(int $serviceId): Service
    {
        $accountId = Auth::user()?->currentAccountId();
        abort_unless($accountId, 403);

        return Service::query()
            ->where('account_id', $accountId)
            ->with(['serviceType', 'translations.language.locale'])
            ->findOrFail($serviceId);
    }

    public function render(): View
    {
        return view('livewire.catalog.copy-provider-service-modal');
    }
}
