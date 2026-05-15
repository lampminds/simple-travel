@extends('layouts.base', ['title' => __('catalog.title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('catalog.title') }}</h3>
                        <p class="mt-1 fw-medium">
                            @if ($mode === 'provider')
                                {{ __('catalog.provider_intro') }}
                            @elseif ($mode === 'agency')
                                {{ __('catalog.agency_intro') }}
                            @elseif ($mode === 'operator')
                                {{ __('catalog.operator_intro') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="row">
                    <div class="col-lg-12 mt-2">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('catalog.dismiss_alert') }}"></button>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $catalogHideFilterAndOwnServicesList = ($catalogTypeFilter ?? null) === null && $services->isEmpty();
            @endphp

            @unless ($catalogHideFilterAndOwnServicesList)
                <div class="row mt-3">
                    <div class="col-md-6 col-lg-4">
                        <form method="get" action="{{ route('catalog') }}" id="catalog-type-filter-form">
                            <label for="catalog-type-filter" class="form-label mb-1">{{ __('catalog.filter_by_type') }}</label>
                            <select
                                name="type"
                                id="catalog-type-filter"
                                class="form-select"
                                style="max-width: 100%;"
                                aria-label="{{ __('catalog.filter_by_type') }}"
                                onchange="document.getElementById('catalog-type-filter-form').submit()"
                            >
                                <option value="all" @selected(($catalogTypeFilter ?? null) === null)>{{ __('catalog.filter_type_all') }}</option>
                                @foreach ($catalogServiceTypeOptions ?? [] as $code => $label)
                                    <option value="{{ $code }}" @selected(($catalogTypeFilter ?? null) === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            @endunless

            @if ($mode === 'provider')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => false,
                    'hideOwnServicesHeadingAndList' => $catalogHideFilterAndOwnServicesList,
                ])
            @elseif ($mode === 'operator')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => true,
                    'servicesSectionTitle' => __('catalog.operator_own_heading'),
                    'hideOwnServicesHeadingAndList' => $catalogHideFilterAndOwnServicesList,
                ])
                @if (isset($linkedCatalog) && $linkedCatalog->isNotEmpty())
                    @include('catalog.partials.operator-linked-catalog', ['linkedCatalog' => $linkedCatalog])
                @endif
            @elseif ($mode === 'agency')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => true,
                    'hideOwnServicesHeadingAndList' => $catalogHideFilterAndOwnServicesList,
                ])
            @endif

            <div class="mt-4">
                <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary">
                    {{ __('catalog.back_dashboard') }}
                </a>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
