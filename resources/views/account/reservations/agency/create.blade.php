@extends('layouts.base', ['title' => __('account.reservations.create_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.reservations.create_heading')"
                        :instructions="__('account.reservations.create_intro')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <dl class="row mb-4">
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_operator') }}</dt>
                                <dd class="col-sm-8">{{ $operatorLabel }}</dd>
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_package') }}</dt>
                                <dd class="col-sm-8">{{ $packageLabel }}</dd>
                                <dt class="col-sm-4">{{ __('account.package_offers.agency_index_col_price') }}</dt>
                                <dd class="col-sm-8 @if ($agencyPrice['has_amount'] ?? false) fw-medium @else text-muted @endif">
                                    {{ $agencyPrice['formatted'] ?? '—' }}
                                </dd>
                            </dl>

                            <form method="POST" action="{{ route('account.reservations.store') }}">
                                @csrf
                                <input type="hidden" name="package_offer_uuid" value="{{ $offer->uuid }}">

                                <h6 class="mb-2">{{ __('account.reservations.passengers_heading') }}</h6>
                                <p class="text-muted small mb-3">{{ __('account.reservations.passengers_intro') }}</p>

                                <div class="row g-3 mb-4">
                                    @foreach (\App\Support\BookingPassengersSnapshot::types() as $passengerType)
                                        @php
                                            $defaultValue = $passengerType === 'adult' ? 1 : 0;
                                            $fieldValue = old('passengers.'.$passengerType, $defaultValue);
                                        @endphp
                                        <div class="col-6 col-md-3">
                                            <label for="passengers_{{ $passengerType }}" class="form-label">
                                                {{ __('account.reservations.passenger_types.'.$passengerType) }}
                                            </label>
                                            <input
                                                type="number"
                                                name="passengers[{{ $passengerType }}]"
                                                id="passengers_{{ $passengerType }}"
                                                class="form-control @error('passengers.'.$passengerType) is-invalid @enderror @error('passengers') is-invalid @enderror"
                                                value="{{ $fieldValue }}"
                                                min="0"
                                                max="999"
                                                required
                                            >
                                            @error('passengers.'.$passengerType)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                                @error('passengers')
                                    <div class="alert alert-danger py-2" role="alert">{{ $message }}</div>
                                @enderror

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="travel_start_date" class="form-label">{{ __('account.reservations.field_travel_start') }}</label>
                                        <input
                                            type="date"
                                            name="travel_start_date"
                                            id="travel_start_date"
                                            class="form-control @error('travel_start_date') is-invalid @enderror"
                                            value="{{ old('travel_start_date') }}"
                                            required
                                        >
                                        @error('travel_start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="travel_end_date" class="form-label">{{ __('account.reservations.field_travel_end') }}</label>
                                        <input
                                            type="date"
                                            name="travel_end_date"
                                            id="travel_end_date"
                                            class="form-control @error('travel_end_date') is-invalid @enderror"
                                            value="{{ old('travel_end_date') }}"
                                            required
                                        >
                                        @error('travel_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="remarks_customer" class="form-label">{{ __('account.reservations.field_remarks') }}</label>
                                        <textarea
                                            name="remarks_customer"
                                            id="remarks_customer"
                                            rows="3"
                                            class="form-control @error('remarks_customer') is-invalid @enderror"
                                        >{{ old('remarks_customer') }}</textarea>
                                        @error('remarks_customer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @error('package_offer')
                                    <div class="alert alert-danger mt-3 mb-0" role="alert">{{ $message }}</div>
                                @enderror

                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('account.reservations.create_submit') }}
                                    </button>
                                    <a href="{{ route('account.reservations.index') }}" class="btn btn-outline-secondary">
                                        {{ __('account.reservations.create_cancel') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
