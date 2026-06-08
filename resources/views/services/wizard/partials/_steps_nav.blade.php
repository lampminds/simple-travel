{{--
    Wizard step navigation (horizontal strip below the site navbar).

    Required:
      - $serviceType: ServiceType model
      - $currentStep: int 1–8

    Optional:
      - $service: Service model; when null, nothing is rendered (e.g. step 1 create).

    Current step is shown as a highlighted disabled control; other steps are links.
--}}
@php
    $service = $service ?? null;

    $stepNavigations = [
        1 => ['route' => 'services.wizard.step1.edit', 'label' => __('wizard.nav_to_step1')],
        2 => ['route' => 'services.wizard.step2', 'label' => __('wizard.nav_to_step2')],
        3 => ['route' => 'services.wizard.step3', 'label' => __('wizard.nav_to_step3')],
        4 => ['route' => 'services.wizard.step4', 'label' => __('wizard.nav_to_step4')],
        5 => ['route' => 'services.wizard.step5', 'label' => __('wizard.nav_to_step5')],
        6 => ['route' => 'services.wizard.step6', 'label' => __('wizard.nav_to_step6')],
        7 => ['route' => 'services.wizard.step7', 'label' => __('wizard.nav_to_step7')],
    ];
    if (\App\Support\ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($serviceType->code ?? null)) {
        unset($stepNavigations[4]);
    }
    if (\App\Support\ServiceWizardStepEight::isEnabledForServiceTypeCode($serviceType->code ?? null)) {
        $step8Label = ($serviceType->code ?? '') === 'transfer'
            ? __('wizard.nav_to_step8_transfer')
            : __('wizard.nav_to_step8');
        $stepNavigations[8] = ['route' => 'services.wizard.step8', 'label' => $step8Label];
    }
@endphp
@if ($service !== null)
    <div class="wizard-steps-nav bg-white border-bottom py-2 shadow-sm">
        <div class="container">
            <nav
                class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start align-items-center"
                aria-label="{{ __('wizard.steps_nav_aria') }}"
            >
                @foreach ($stepNavigations as $stepNum => $nav)
                    @if ($stepNum === $currentStep)
                        <span
                            class="btn btn-sm btn-primary disabled wizard-step-nav-current"
                            tabindex="-1"
                            aria-current="page"
                        >{{ $nav['label'] }}</span>
                    @else
                        <a
                            href="{{ route($nav['route'], ['serviceType' => $serviceType->code, 'service' => $service]) }}"
                            class="btn btn-sm btn-outline-primary"
                        >{{ $nav['label'] }}</a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
@endif
