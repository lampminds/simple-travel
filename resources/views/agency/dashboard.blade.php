@extends('layouts.base', ['title' => __('agency_dashboard.title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('agency_dashboard.title') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('agency_dashboard.intro') }}</p>
                    </div>
                </div>
            </div>

            <x-panel-account-summary :stats="$panelStats" class="mt-4" />

            @include('dashboard.partials.currency-rates-chart-section')

            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('account.package-offers.index') }}" class="btn btn-outline-primary">
                    {{ __('account.package_offers_nav') }}
                </a>
            </div>

            @if (auth()->user()?->shouldShowBackToAccountDashboard())
                <a href="{{ route('account.dashboard') }}" class="btn btn-outline-secondary mt-4">
                    {{ __('catalog.back_dashboard') }}
                </a>
            @endif
        </div>
    </section>

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    @vite('resources/js/operator-currency-rates-chart.js')
@endsection
