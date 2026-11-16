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
                <div class="col-lg-12">
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
                                    $passengerFields = $passengerFields ?? [];
                                    $bookingFields = $bookingFields ?? [];
                                    $countries = $countries ?? collect();
                                    $passengerFieldValues = old('passenger_fields');
                                    if (! is_array($passengerFieldValues)) {
                                        $passengerFieldValues = is_array($submittedInput['passenger_fields'] ?? null)
                                            ? $submittedInput['passenger_fields']
                                            : [];
                                    }
                                    $bookingFieldValues = old('booking_fields');
                                    if (! is_array($bookingFieldValues)) {
                                        $bookingFieldValues = is_array($submittedInput['booking_fields'] ?? null)
                                            ? $submittedInput['booking_fields']
                                            : [];
                                    }
                                @endphp

                                <h6 class="mb-2">{{ __('account.reservations.passengers_heading') }}</h6>
                                <p class="text-muted small mb-3">{{ __('account.reservations.passengers_intro') }}</p>
                                @if ($passengerFields !== [])
                                    <p class="text-muted small mb-3">{{ __('account.reservations.extra_passenger_intro') }}</p>
                                @endif

                                <div id="passenger-ages" class="mb-2">
                                    @foreach ($ageValues as $index => $ageValue)
                                        @include('account.reservations.agency.partials.passenger-row', [
                                            'ageValue' => $ageValue,
                                            'passengerIndex' => $index,
                                            'passengerFields' => $passengerFields,
                                            'passengerFieldValues' => $passengerFieldValues,
                                            'countries' => $countries,
                                            'showErrors' => true,
                                        ])
                                    @endforeach
                                </div>
                                @error('ages')
                                    <div class="alert alert-danger py-2" role="alert">{{ $message }}</div>
                                @enderror
                                <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-passenger-age">
                                    {{ __('account.reservations.add_passenger') }}
                                </button>

                                @if ($bookingFields !== [])
                                    <h6 class="mb-2">{{ __('account.reservations.extra_booking_heading') }}</h6>
                                    <p class="text-muted small mb-3">{{ __('account.reservations.extra_booking_intro') }}</p>
                                    <div class="row g-3 mb-4">
                                        @foreach ($bookingFields as $field)
                                            @php
                                                $bookingValue = $bookingFieldValues[$field->id] ?? $bookingFieldValues[(string) $field->id] ?? '';
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="form-label" for="booking-field-{{ $field->id }}">
                                                    {{ $field->label }}
                                                    @if ($field->required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                @if ($field->inputType === \App\Models\ProviderBookingField::TYPE_TEXTAREA)
                                                    <textarea
                                                        id="booking-field-{{ $field->id }}"
                                                        name="booking_fields[{{ $field->id }}]"
                                                        rows="2"
                                                        class="form-control @error('booking_fields.'.$field->id) is-invalid @enderror"
                                                        maxlength="2000"
                                                        @if ($field->required) required @endif
                                                    >{{ $bookingValue }}</textarea>
                                                @elseif ($field->inputType === \App\Models\ProviderBookingField::TYPE_COUNTRY)
                                                    <select
                                                        id="booking-field-{{ $field->id }}"
                                                        name="booking_fields[{{ $field->id }}]"
                                                        class="form-select @error('booking_fields.'.$field->id) is-invalid @enderror"
                                                        @if ($field->required) required @endif
                                                    >
                                                        <option value="">{{ __('account.booking_fields.country_placeholder') }}</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}" @selected((string) $bookingValue === (string) $country->id)>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input
                                                        id="booking-field-{{ $field->id }}"
                                                        type="{{ match ($field->inputType) {
                                                            \App\Models\ProviderBookingField::TYPE_DATE => 'date',
                                                            \App\Models\ProviderBookingField::TYPE_TIME => 'time',
                                                            \App\Models\ProviderBookingField::TYPE_PHONE => 'tel',
                                                            \App\Models\ProviderBookingField::TYPE_NUMBER => 'number',
                                                            default => 'text',
                                                        } }}"
                                                        name="booking_fields[{{ $field->id }}]"
                                                        class="form-control @error('booking_fields.'.$field->id) is-invalid @enderror"
                                                        value="{{ $bookingValue }}"
                                                        @if ($field->inputType === \App\Models\ProviderBookingField::TYPE_NUMBER) step="any" inputmode="decimal" @endif
                                                        @if ($field->inputType === \App\Models\ProviderBookingField::TYPE_PHONE) maxlength="40" @elseif ($field->inputType === \App\Models\ProviderBookingField::TYPE_TEXT) maxlength="255" @endif
                                                        @if ($field->required) required @endif
                                                    >
                                                @endif
                                                @if ($field->help)
                                                    <div class="form-text">{{ $field->help }}</div>
                                                @endif
                                                @error('booking_fields.'.$field->id)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

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
                                @include('account.reservations.agency.partials.passenger-row', [
                                    'ageValue' => '',
                                    'passengerIndex' => null,
                                    'passengerFields' => $passengerFields ?? [],
                                    'passengerFieldValues' => [],
                                    'countries' => $countries ?? collect(),
                                    'showErrors' => false,
                                ])
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
