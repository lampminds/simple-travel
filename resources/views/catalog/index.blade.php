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

            <div class="row mt-3">
                <div class="col-md-6 col-lg-4">
                    <form method="get" action="{{ route('catalog') }}" id="catalog-status-filter-form">
                        <label for="catalog-status-filter" class="form-label mb-1">{{ __('catalog.filter_by_status') }}</label>
                        <select
                            name="status"
                            id="catalog-status-filter"
                            class="form-select"
                            aria-label="{{ __('catalog.filter_by_status') }}"
                            onchange="document.getElementById('catalog-status-filter-form').submit()"
                        >
                            <option value="all" @selected(($catalogStatusFilter ?? null) === null)>{{ __('catalog.filter_status_all') }}</option>
                            @foreach ($catalogServiceStatusOptions ?? [] as $value => $label)
                                <option value="{{ $value }}" @selected(($catalogStatusFilter ?? null) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @if ($mode === 'provider')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => false,
                ])
            @elseif ($mode === 'operator')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => true,
                    'servicesSectionTitle' => __('catalog.operator_own_heading'),
                ])
                @if (isset($linkedCatalog) && $linkedCatalog->isNotEmpty())
                    @include('catalog.partials.operator-linked-catalog', ['linkedCatalog' => $linkedCatalog])
                @endif
            @elseif ($mode === 'agency')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => true,
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
