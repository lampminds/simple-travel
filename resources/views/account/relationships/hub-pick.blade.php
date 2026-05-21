@extends('layouts.base', ['title' => __('account.relationships.hub_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.relationships.hub_heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">{{ __('account.relationships.hub_intro') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ __('account.relationships.hub_card_provider_title') }}</h5>
                            <p class="text-muted small flex-grow-1">{{ __('account.relationships.hub_card_provider_desc') }}</p>
                            <a href="{{ route('account.relationships.index', ['as' => 'provider']) }}" class="btn btn-primary">
                                {{ __('account.relationships.hub_card_provider_button') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ __('account.relationships.hub_card_operator_title') }}</h5>
                            <p class="text-muted small flex-grow-1">{{ __('account.relationships.hub_card_operator_desc') }}</p>
                            <a href="{{ route('account.relationships.index', ['as' => 'operator']) }}" class="btn btn-primary">
                                {{ __('account.relationships.hub_card_operator_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
