{{--
    Catalog services list and actions row.
    Expects: $services, $serviceTypes, optional $showRequestFromProviders (bool, default false).
    Optional $hideOwnServicesHeadingAndList: when true, skips title + table/empty alert (first-time empty catalog with "all" filter).
    When $showRequestFromProviders is true, the new-service dropdown and "request from providers" sit in two boxed columns.
--}}
@php
    $showRequestFromProviders = $showRequestFromProviders ?? false;
    $servicesSectionTitle = $servicesSectionTitle ?? null;
    $hideOwnServicesHeadingAndList = $hideOwnServicesHeadingAndList ?? false;
@endphp
@unless ($hideOwnServicesHeadingAndList)
    <div id="provider-services-list" class="row mt-4" tabindex="-1">
        <div class="col-lg-12">
            <h4 class="h5 mb-3">{{ $servicesSectionTitle ?? __('wizard.provider_services_title') }}</h4>

            @if ($services->isEmpty())
                <div class="alert alert-light border mb-3" role="status">
                    {{ __('wizard.provider_services_empty') }}
                </div>
            @else
                @livewire('catalog.provider-services-table', ['services' => $services], key('provider-services-table-'.$services->pluck('id')->join('-')))
            @endif
        </div>
    </div>
    @livewire('catalog.copy-provider-service-modal', key('copy-provider-service-modal'))
@endunless

@php
    $newServiceColumnClass = $showRequestFromProviders
        ? 'col-12 col-md-6 col-xl-5 d-flex'
        : 'col-12 col-md-8 col-lg-6 col-xl-5';
@endphp

<div class="row {{ ($hideOwnServicesHeadingAndList ?? false) ? 'mt-3' : 'mt-4' }}{{ $showRequestFromProviders ? ' g-4 align-items-stretch' : '' }}">
    <div class="{{ $newServiceColumnClass }}">
        @if ($showRequestFromProviders)
            <div class="w-100 card border shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
        @endif
                    <h2 class="h5 {{ $showRequestFromProviders ? 'card-title' : '' }} text-body mb-3">{{ __('catalog.create_new_service_heading') }}</h2>
                    @if ($serviceTypes->isEmpty())
                        <div class="alert alert-warning mb-0" role="alert">
                            {{ __('wizard.provider_no_service_types') }}
                        </div>
                    @else
                        <div class="catalog-new-service-type-wrap" style="max-width: min(100%, 28rem);">
                            <select
                                id="provider-new-service-type"
                                class="form-select w-100"
                                aria-label="{{ __('wizard.provider_new_service_placeholder') }}"
                                onchange="if (this.value) { window.location.href = this.value; }"
                            >
                                <option value="">{{ __('wizard.provider_new_service_placeholder') }}</option>
                                @foreach ($serviceTypes as $serviceType)
                                    <option value="{{ route('services.wizard.step1', ['serviceType' => $serviceType->code]) }}">
                                        {{ $serviceType->dropdown_label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
        @if ($showRequestFromProviders)
                </div>
            </div>
        @endif
    </div>
    @if ($showRequestFromProviders)
        <div class="col-12 col-md-6 col-xl-5 offset-xl-1 d-flex">
            <div class="card border shadow-sm w-100 h-100">
                <div class="card-body p-3 p-md-4">
                    @include('catalog.partials.request-from-providers')
                </div>
            </div>
        </div>
    @endif
</div>
