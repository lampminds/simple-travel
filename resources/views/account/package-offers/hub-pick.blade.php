@extends('layouts.base', ['title' => __('account.package_offers.hub_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.package_offers.hub_heading')"
                        :instructions="__('account.package_offers.hub_intro')"
                    />
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('account.package_offers.hub_operator_card_title') }}</h2>
                            <p class="text-muted small">{{ __('account.package_offers.hub_operator_card_intro') }}</p>
                            <a href="{{ route('account.package-offers.index', ['as' => 'operator']) }}" class="btn btn-primary">
                                {{ __('account.package_offers.hub_operator_card_action') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5">{{ __('account.package_offers.hub_agency_card_title') }}</h2>
                            <p class="text-muted small">{{ __('account.package_offers.hub_agency_card_intro') }}</p>
                            <a href="{{ route('account.package-offers.index', ['as' => 'agency']) }}" class="btn btn-primary">
                                {{ __('account.package_offers.hub_agency_card_action') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
