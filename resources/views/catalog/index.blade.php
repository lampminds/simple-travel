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

            @if ($mode === 'provider')
                @include('catalog.partials.provider-services', [
                    'services' => $services,
                    'serviceTypes' => $serviceTypes,
                    'showRequestFromProviders' => false,
                ])
            @elseif (in_array($mode, ['operator', 'agency'], true))
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
