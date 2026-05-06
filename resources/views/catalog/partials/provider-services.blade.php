{{--
    Catalog services list and actions row.
    Expects: $services, $serviceTypes, optional $showRequestFromProviders (bool, default false).
    When $showRequestFromProviders is true, the new-service dropdown and "request from providers" sit in two boxed columns.
--}}
@php
    $showRequestFromProviders = $showRequestFromProviders ?? false;
    $servicesSectionTitle = $servicesSectionTitle ?? null;
@endphp
<div class="row mt-4">
    <div class="col-lg-12">
        <h4 class="h5 mb-3">{{ $servicesSectionTitle ?? __('wizard.provider_services_title') }}</h4>

        @if ($services->isEmpty())
            <div class="alert alert-light border mb-3" role="status">
                {{ __('wizard.provider_services_empty') }}
            </div>
        @else
            <table class="table table-hover align-middle bg-white rounded border">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 76px;">{{ __('wizard.provider_services_col_thumb') }}</th>
                        <th scope="col">{{ __('wizard.provider_services_col_name') }}</th>
                        <th scope="col">{{ __('wizard.provider_services_col_type') }}</th>
                        <th scope="col">{{ __('wizard.provider_services_col_status') }}</th>
                        <th scope="col" class="text-center">{{ __('wizard.provider_services_col_variants') }}</th>
                        <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $svc)
                        <tr>
                            <td class="text-center align-middle">
                                @if ($url = $svc->mainImageUrl(\App\Models\Service::MEDIA_CONVERSION_THUMBNAIL))
                                    <img src="{{ $url }}" alt="" class="rounded border" style="width: 56px; height: 56px; object-fit: cover;">
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>{{ $svc->name !== '' ? $svc->name : ('#'.$svc->id) }}</td>
                            <td>{{ $svc->serviceType?->name ?: strtoupper($svc->serviceType?->code ?? '') }}</td>
                            <td>
                                @php $st = $svc->status ?? ''; @endphp
                                {{ $st !== '' ? __('filament.resources.service_status.'.$st) : '—' }}
                            </td>
                            <td class="text-center">
                                {{ (int) ($svc->service_variants_count ?? 0) }}
                            </td>
                            <td class="text-end text-nowrap">
                                @if ($svc->serviceType)
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            aria-label="{{ __('wizard.provider_services_actions_menu') }}"
                                        >
                                            <i class="icon icon-xs text-primary" data-feather="more-horizontal" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a
                                                    href="{{ route('services.wizard.step1.edit', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                    class="dropdown-item"
                                                >
                                                        <i class="icon-xxs icon me-2 text-primary" data-feather="edit-3" aria-hidden="true"></i>
                                                    {{ __('wizard.provider_services_action_step1') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('services.wizard.step2', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                    class="dropdown-item"
                                                >
                                                        <i class="icon-xxs icon me-2 text-success" data-feather="toggle-right" aria-hidden="true"></i>
                                                    {{ __('wizard.provider_services_action_step2') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('services.wizard.step3', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                    class="dropdown-item"
                                                >
                                                        <i class="icon-xxs icon me-2 text-info" data-feather="sliders" aria-hidden="true"></i>
                                                    {{ __('wizard.provider_services_action_step3') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('services.wizard.step4', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                    class="dropdown-item"
                                                >
                                                        <i class="icon-xxs icon me-2 text-warning" data-feather="layers" aria-hidden="true"></i>
                                                    {{ __('wizard.provider_services_action_step4') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('services.wizard.step5', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                    class="dropdown-item"
                                                >
                                                        <i class="icon-xxs icon me-2 text-danger" data-feather="image" aria-hidden="true"></i>
                                                    {{ __('wizard.provider_services_action_step5') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@php
    $newServiceColumnClass = $showRequestFromProviders
        ? 'col-12 col-md-6 col-xl-5 d-flex'
        : 'col-12 col-xl-8';
@endphp

<div class="row mt-4{{ $showRequestFromProviders ? ' g-4 align-items-stretch' : '' }}">
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
                        <select
                            id="provider-new-service-type"
                            class="form-select form-select-lg"
                            aria-label="{{ __('wizard.provider_new_service_placeholder') }}"
                            onchange="if (this.value) { window.location.href = this.value; }"
                        >
                            <option value="">{{ __('wizard.provider_new_service_placeholder') }}</option>
                            @foreach ($serviceTypes as $serviceType)
                                <option value="{{ route('services.wizard.step1', ['serviceType' => $serviceType->code]) }}">
                                    {{ $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code) }}
                                </option>
                            @endforeach
                        </select>
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
