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
                                <dd class="col-sm-8">
                                    {{ $packageLabel }}
                                    @include('account.partials.catalog-kind-badge', ['catalog' => $offer->catalog])
                                </dd>
                                <dt class="col-sm-4">{{ __('account.reservations.reference_price') }}</dt>
                                <dd class="col-sm-8 @if ($agencyPrice['has_amount'] ?? false) @else text-muted @endif">
                                    {{ $agencyPrice['formatted'] ?? '—' }}
                                </dd>
                                @if ($quotedPrice !== null)
                                    <dt class="col-sm-4">{{ __('account.reservations.quoted_price') }}</dt>
                                    <dd class="col-sm-8 @if ($quotedPrice['has_amount'] ?? false) fw-medium @else text-muted @endif">
                                        {{ ($quotedPrice['has_amount'] ?? false) ? ($quotedPrice['formatted'] ?? '—') : __('account.reservations.validation.price_not_available') }}
                                    </dd>
                                @endif
                            </dl>

                            <form method="POST" action="{{ route('account.reservations.store') }}">
                                @csrf
                                <input type="hidden" name="package_offer_uuid" value="{{ $offer->uuid }}">

                                @php
                                    $submittedInput = is_array($reservationInput ?? null) ? $reservationInput : [];
                                    $submittedAges = $submittedInput['ages'] ?? null;
                                    $ageValues = old('ages', is_array($submittedAges) && $submittedAges !== [] ? $submittedAges : ['']);
                                    if (! is_array($ageValues) || $ageValues === []) {
                                        $ageValues = [''];
                                    }
                                @endphp

                                <h6 class="mb-2">{{ __('account.reservations.passengers_heading') }}</h6>
                                <p class="text-muted small mb-3">{{ __('account.reservations.passengers_intro') }}</p>

                                <div id="passenger-ages" class="mb-2">
                                    @foreach ($ageValues as $index => $ageValue)
                                        <div class="row g-2 align-items-end mb-2 passenger-age-row">
                                            <div class="col-6 col-md-4">
                                                <label class="form-label" @if ($index === 0) for="passenger_age_0" @endif>
                                                    {{ __('account.reservations.field_age') }}
                                                </label>
                                                <input
                                                    type="number"
                                                    name="ages[]"
                                                    @if ($index === 0) id="passenger_age_0" @endif
                                                    class="form-control @error('ages.'.$index) is-invalid @enderror @error('ages') is-invalid @enderror"
                                                    value="{{ $ageValue }}"
                                                    min="0"
                                                    max="120"
                                                    required
                                                >
                                                @error('ages.'.$index)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-outline-secondary remove-passenger-age">
                                                    {{ __('account.reservations.remove_passenger') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('ages')
                                    <div class="alert alert-danger py-2" role="alert">{{ $message }}</div>
                                @enderror
                                <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-passenger-age">
                                    {{ __('account.reservations.add_passenger') }}
                                </button>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="travel_start_date" class="form-label">{{ __('account.reservations.field_travel_start') }}</label>
                                        <input
                                            type="date"
                                            name="travel_start_date"
                                            id="travel_start_date"
                                            class="form-control @error('travel_start_date') is-invalid @enderror"
                                            value="{{ old('travel_start_date', $submittedInput['travel_start_date'] ?? '') }}"
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
                                            value="{{ old('travel_end_date', $submittedInput['travel_end_date'] ?? '') }}"
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
                                        >{{ old('remarks_customer', $submittedInput['remarks_customer'] ?? '') }}</textarea>
                                        @error('remarks_customer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @error('package_offer')
                                    <div class="alert alert-danger mt-3 mb-0" role="alert">{{ $message }}</div>
                                @enderror

                                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                                    <a href="{{ route('account.reservations.index') }}" class="btn btn-outline-secondary">
                                        {{ __('account.reservations.create_cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-outline-primary" formaction="{{ route('account.reservations.preview') }}">
                                        {{ __('account.reservations.preview_price') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('account.reservations.create_submit') }}
                                    </button>
                                </div>
                            </form>
                            <template id="passenger-age-template">
                                <div class="row g-2 align-items-end mb-2 passenger-age-row">
                                    <div class="col-6 col-md-4">
                                        <label class="form-label">{{ __('account.reservations.field_age') }}</label>
                                        <input type="number" name="ages[]" class="form-control" min="0" max="120" required>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary remove-passenger-age">
                                            {{ __('account.reservations.remove_passenger') }}
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <script>
                                (function () {
                                    var list = document.getElementById('passenger-ages');
                                    var addButton = document.getElementById('add-passenger-age');
                                    var template = document.getElementById('passenger-age-template');
                                    if (!list || !addButton || !template) {
                                        return;
                                    }
                                    addButton.addEventListener('click', function () {
                                        if (list.querySelectorAll('.passenger-age-row').length >= 30) {
                                            return;
                                        }
                                        list.appendChild(template.content.cloneNode(true));
                                    });
                                    list.addEventListener('click', function (event) {
                                        var button = event.target.closest('.remove-passenger-age');
                                        if (!button) {
                                            return;
                                        }
                                        var rows = list.querySelectorAll('.passenger-age-row');
                                        if (rows.length < 2) {
                                            return;
                                        }
                                        button.closest('.passenger-age-row').remove();
                                    });
                                })();
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
