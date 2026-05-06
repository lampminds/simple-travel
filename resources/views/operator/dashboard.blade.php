@extends('layouts.base', ['title' => 'Prompt - Panel de operador'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('operator_dashboard.title') }}</h3>
                    </div>
                </div>
            </div>

            <x-panel-account-summary :stats="$panelStats" class="mt-4" />

            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('account.service-offers.index') }}" class="btn btn-outline-primary">
                    {{ __('account.service_offers_nav') }}
                </a>
                @if (auth()->user()?->shouldShowBackToAccountDashboard())
                    <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-secondary">
                        {{ __('catalog.back_dashboard') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
