<?php

namespace App\Livewire\ServiceWizard;

use App\Models\ProviderPassengerPolicy;
use App\Models\Service;
use App\Models\ServiceType;
use App\Services\PassengerPolicyWriter;
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

    public ?string $catalogStatusHelpHtml = null;

    public ?string $catalogFeaturedHelpHtml = null;

    public ?string $catalogPublicHelpHtml = null;

    public ?string $catalogConfirmationTimeHoursHelpHtml = null;

    public function mount(
        int $serviceId,
        int $serviceTypeId,
        ?string $catalogStatusHelpHtml = null,
        ?string $catalogFeaturedHelpHtml = null,
        ?string $catalogPublicHelpHtml = null,
        ?string $catalogConfirmationTimeHoursHelpHtml = null,
    ): void {
        $this->serviceId = $serviceId;
        $this->serviceTypeId = $serviceTypeId;
        $this->catalogStatusHelpHtml = $catalogStatusHelpHtml;
        $this->catalogFeaturedHelpHtml = $catalogFeaturedHelpHtml;
        $this->catalogPublicHelpHtml = $catalogPublicHelpHtml;
        $this->catalogConfirmationTimeHoursHelpHtml = $catalogConfirmationTimeHoursHelpHtml;

        $service = $this->authorizedService();

        $this->form = [
            'status' => (string) ($service->status ?: 'onhold'),
            'is_featured' => (bool) $service->is_featured,
            'is_public' => (bool) $service->is_public,
            'provider_passenger_policy_id' => $service->provider_passenger_policy_id !== null
                ? (string) $service->provider_passenger_policy_id
                : '',
            'booking_mode' => (string) ($service->booking_mode ?: 'instant'),
            'confirmation_time_hours' => $service->confirmation_time_hours !== null
                ? (string) $service->confirmation_time_hours
                : '',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules(), [], $this->validationAttributes());

        $service = $this->authorizedService();

        if ($validated['form']['status'] === 'active' && ! $service->canBeActivated()) {
            $this->addError('form.status', $service->isCombo()
                ? __('wizard.step2_active_requires_components')
                : __('wizard.step2_active_requires_variants'));

            return;
        }

        $policyId = $validated['form']['provider_passenger_policy_id'];
        $policyId = $policyId === '' || $policyId === null ? null : (int) $policyId;

        $service->update([
            'status' => $validated['form']['status'],
            'is_featured' => (bool) $validated['form']['is_featured'],
            'is_public' => (bool) $validated['form']['is_public'],
            'provider_passenger_policy_id' => $policyId,
            'booking_mode' => $validated['form']['booking_mode'],
            'confirmation_time_hours' => $validated['form']['confirmation_time_hours'] === ''
                ? null
                : (int) $validated['form']['confirmation_time_hours'],
        ]);

        $service->unsetRelation('passengerPolicy');
        app(PassengerPolicyWriter::class)->deletePricesOutsidePolicy($service);

        session()->flash('status', __('wizard.step2_saved'));

        $serviceType = ServiceType::query()->findOrFail($this->serviceTypeId);

        $this->redirectRoute('services.wizard.step3', [
            'serviceType' => $serviceType->code,
            'service' => $service,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $accountId = (int) $this->authorizedService()->account_id;

        return [
            'form.status' => ['required', Rule::in(['active', 'onhold', 'suspended', 'discontinued', 'inactive', 'terminated'])],
            'form.is_featured' => ['required', 'boolean'],
            'form.is_public' => ['required', 'boolean'],
            'form.provider_passenger_policy_id' => [
                'nullable',
                'integer',
                Rule::exists('provider_passenger_policies', 'id')->where('provider_id', $accountId),
            ],
            'form.booking_mode' => ['required', Rule::in(['instant', 'request', 'external', 'quote'])],
            'form.confirmation_time_hours' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        $f = 'wizard.step2_fields';

        return [
            'form.status' => __($f.'.status'),
            'form.is_featured' => __($f.'.is_featured'),
            'form.is_public' => __($f.'.is_public'),
            'form.provider_passenger_policy_id' => __($f.'.passenger_policy'),
            'form.booking_mode' => __($f.'.booking_mode'),
            'form.confirmation_time_hours' => __($f.'.confirmation_time_hours'),
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
        $service = $this->authorizedService();
        $canActivate = $service->canBeActivated();

        return view('livewire.service-wizard.service-status-step', [
            'canActivateService' => $canActivate,
            'passengerPolicies' => ProviderPassengerPolicy::query()
                ->where('provider_id', $service->account_id)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
