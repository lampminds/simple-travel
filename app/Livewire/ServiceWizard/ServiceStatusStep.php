<?php

namespace App\Livewire\ServiceWizard;

use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ServiceStatusStep extends Component
{
    public int $serviceId;

    public int $serviceTypeId;

    /** @var array<string, mixed> */
    public array $form = [];

    public function mount(int $serviceId, int $serviceTypeId): void
    {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;

        $service = $this->authorizedService();

        $this->form = [
            'status' => (string) ($service->status ?: 'onhold'),
            'duration_minutes' => $service->duration_minutes !== null ? (string) $service->duration_minutes : '',
            'is_bookable' => (bool) $service->is_bookable,
            'is_featured' => (bool) $service->is_featured,
            'is_public' => (bool) $service->is_public,
            'booking_mode' => $service->booking_mode !== null ? (string) $service->booking_mode : '',
            'confirmation_time_hours' => $service->confirmation_time_hours !== null
                ? (string) $service->confirmation_time_hours
                : '',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $service = $this->authorizedService();
        $service->update([
            'status' => $validated['form']['status'],
            'duration_minutes' => $validated['form']['duration_minutes'] === '' ? null : (int) $validated['form']['duration_minutes'],
            'is_bookable' => (bool) $validated['form']['is_bookable'],
            'is_featured' => (bool) $validated['form']['is_featured'],
            'is_public' => (bool) $validated['form']['is_public'],
            'booking_mode' => $validated['form']['booking_mode'] === '' ? null : $validated['form']['booking_mode'],
            'confirmation_time_hours' => $validated['form']['confirmation_time_hours'] === ''
                ? null
                : (int) $validated['form']['confirmation_time_hours'],
        ]);

        session()->flash('status', __('wizard.step2_saved'));

        $serviceType = ServiceType::query()->findOrFail($this->serviceTypeId);

        $this->redirectRoute('services.wizard.step3', [
            'serviceType' => $serviceType->code,
            'service' => $service->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'form.status' => ['required', Rule::in(['active', 'onhold', 'suspended', 'discontinued', 'inactive', 'terminated'])],
            'form.duration_minutes' => ['nullable', 'integer', 'min:0'],
            'form.is_bookable' => ['required', 'boolean'],
            'form.is_featured' => ['required', 'boolean'],
            'form.is_public' => ['required', 'boolean'],
            'form.booking_mode' => ['nullable', Rule::in(['instant', 'request', 'external', 'quote'])],
            'form.confirmation_time_hours' => ['nullable', 'integer', 'min:0'],
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

    public function render(): View
    {
        return view('livewire.service-wizard.service-status-step');
    }
}
