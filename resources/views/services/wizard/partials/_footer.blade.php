{{--
    Wizard footer: back to catalog and dashboard only (step links live in _steps_nav).

    Required variables:
      - $serviceType: ServiceType model
      - $currentStep: Step number (1-8) — kept for callers compatibility; unused here.

    Optional:
      - $service: Service model (unused; step bar is in _steps_nav).
--}}
<div class="d-flex flex-wrap justify-content-start align-items-center gap-2 mt-4 pt-3 border-top">
    <a href="{{ route('catalog') }}" class="btn btn-outline-secondary">@lang('wizard.nav_back')</a>
    <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary">@lang('wizard.nav_dashboard')</a>
</div>
