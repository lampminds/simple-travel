@extends('layouts.base', ['title' => __('account.reservations.show_page_title', ['code' => $booking->booking_code])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.reservations.show_heading', ['code' => $booking->booking_code])"
                        :instructions="__('account.reservations.show_intro')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_operator') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $booking->operatorAccount?->commercial_name ?? $booking->operatorAccount?->name ?? ('#' . $booking->operator_id) }}
                                </dd>
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_package') }}</dt>
                                <dd class="col-sm-8">{{ $packageLabel }}</dd>
                                <dt class="col-sm-4">{{ __('account.reservations.col_travel_dates') }}</dt>
                                <dd class="col-sm-8">
                                    {{ locale_date($booking->travel_start_date) }}
                                    —
                                    {{ locale_date($booking->travel_end_date) }}
                                </dd>
                                <dt class="col-sm-4">{{ __('account.reservations.show_passengers') }}</dt>
                                <dd class="col-sm-8">
                                    {{ \App\Support\BookingPassengersSnapshot::formatSummary($booking->passengers_snapshot) }}
                                </dd>
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_status') }}</dt>
                                <dd class="col-sm-8">{{ $booking->status?->displayLabel() ?? '—' }}</dd>
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_price') }}</dt>
                                <dd class="col-sm-8 fw-medium">{{ $priceBreakdown['grand_total_formatted'] }}</dd>
                            </dl>
                        </div>
                    </div>

                    @include('account.reservations.partials.price-breakdown', ['breakdown' => $priceBreakdown])

                    <a href="{{ route('account.reservations.index') }}" class="btn btn-outline-secondary mt-3">
                        {{ __('account.reservations.show_back') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    @include('partials.feather-livewire-refresh')
@endsection
