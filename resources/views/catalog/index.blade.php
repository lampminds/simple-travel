@extends('layouts.base', ['title' => __('catalog.title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('catalog.title')"
                        :instructions="$mode === 'provider'
                            ? __('catalog.provider_intro')
                            : ($mode === 'agency'
                                ? __('catalog.agency_intro')
                                : __('catalog.operator_intro'))"
                    />
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
                $catalogHideFilterAndOwnServicesList = ($catalogTypeFilter ?? null) === null
                    && ($catalogStatusFilter ?? null) === null
                    && $services->isEmpty();
            @endphp

            @unless ($catalogHideFilterAndOwnServicesList)
                <form method="get" action="{{ route('catalog') }}" id="catalog-filter-form" class="row mt-3 g-3">
                    <div class="col-md-6 col-lg-4">
                        <label for="catalog-type-filter" class="form-label mb-1">{{ __('catalog.filter_by_type') }}</label>
                        <select
                            name="type"
                            id="catalog-type-filter"
                            class="form-select w-100"
                            aria-label="{{ __('catalog.filter_by_type') }}"
                            onchange="document.getElementById('catalog-filter-form').submit()"
                        >
                            <option value="all" @selected(($catalogTypeFilter ?? null) === null)>{{ __('catalog.filter_type_all') }}</option>
                            @foreach ($catalogServiceTypeOptions ?? [] as $code => $label)
                                <option value="{{ $code }}" @selected(($catalogTypeFilter ?? null) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label for="catalog-status-filter" class="form-label mb-1">{{ __('catalog.filter_by_status') }}</label>
                        <select
                            name="status"
                            id="catalog-status-filter"
                            class="form-select w-100"
                            aria-label="{{ __('catalog.filter_by_status') }}"
                            onchange="document.getElementById('catalog-filter-form').submit()"
                        >
                            <option value="all" @selected(($catalogStatusFilter ?? null) === null)>{{ __('catalog.filter_status_default') }}</option>
                            @foreach ($catalogServiceStatusOptions ?? [] as $status => $label)
                                <option value="{{ $status }}" @selected(($catalogStatusFilter ?? null) === $status)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
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

@section('script-bottom')
    @include('partials.feather-livewire-refresh')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash !== '#provider-services-list') {
                return;
            }
            const servicesList = document.getElementById('provider-services-list');
            if (servicesList === null) {
                return;
            }
            servicesList.scrollIntoView({ block: 'start' });
            servicesList.focus({ preventScroll: true });
        });
    </script>
@endsection
