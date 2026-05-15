{{--
    Wizard header (h3 title + subtitle).

    Required variables:
      - $serviceType: ServiceType model (with translations loaded for current locale)
      - $step:        Step number (1-8 in the service wizard)
      - $subtitle:    String shown below the title

    Optional:
      - $service:     Service model. Pass `null` (or omit) when creating a brand new
                      service in step 1, so the placeholder is used instead of the name.

    The h3 follows the format defined by `wizard.header_title`:
        "{type}: {name} - Paso {step}"   (es)
        "{type}: {name} - Step {step}"   (en)
--}}
@php
    $service = $service ?? null;

    $serviceTypeName = $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code);

    if ($service === null) {
        $displayName = __('wizard.header_new_service_placeholder');
    } elseif ($service->name === '') {
        $displayName = __('wizard.service_unnamed');
    } else {
        $displayName = $service->name;
    }

    $pageTitle = __('wizard.header_title', [
        'type' => $serviceTypeName,
        'name' => $displayName,
        'step' => $step,
    ]);
@endphp
<div class="row">
    <div class="col-lg-12">
        <div class="page-title">
            <h3 class="my-0">{{ $pageTitle }}</h3>
            <p class="mt-1 fw-medium">{{ $subtitle }}</p>
        </div>
    </div>
</div>
