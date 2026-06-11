@extends('layouts.base', ['title' => __('account.reservations.index_page_title')])

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
                        :title="__('account.reservations.index_heading')"
                        :instructions="__('account.reservations.index_intro')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ __('account.reservations.bookable_heading') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($bookableOffers->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.reservations.bookable_empty') }}</p>
                                <a href="{{ route('account.package-offers.index', ['as' => 'agency']) }}" class="btn btn-sm btn-outline-primary mt-3">
                                    {{ __('account.reservations.bookable_go_offers') }}
                                </a>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.package_offers.agency_index_col_operator') }}</th>
                                                <th>{{ __('account.package_offers.agency_index_col_package') }}</th>
                                                <th class="text-end">{{ __('account.package_offers.agency_index_col_price') }}</th>
                                                <th class="text-end text-nowrap">{{ __('wizard.provider_services_col_actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bookableOffers as $offer)
                                                @php
                                                    $operatorLabel = $offer->operatorAccount?->commercial_name
                                                        ?? $offer->operatorAccount?->name
                                                        ?? ('#' . $offer->operator_id);
                                                    $agencyPrice = $offer->agency_price ?? ['has_amount' => false, 'formatted' => '—'];
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $operatorLabel }}</td>
                                                    <td>{{ $offer->package_label ?? '—' }}</td>
                                                    <td class="text-end @if ($agencyPrice['has_amount'] ?? false) fw-medium @else text-muted @endif">
                                                        {{ $agencyPrice['formatted'] ?? '—' }}
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-secondary me-1"
                                                            onclick="Livewire.dispatch('open-package-offer-preview', { offerUuid: @js($offer->uuid) })"
                                                        >
                                                            {{ __('account.package_offers.agency_index_preview') }}
                                                        </button>
                                                        <a href="{{ route('account.reservations.create', $offer) }}" class="btn btn-sm btn-primary">
                                                            {{ __('account.reservations.book_action') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ __('account.reservations.bookings_heading') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($bookings->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.reservations.bookings_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.reservations.col_code') }}</th>
                                                <th>{{ __('account.package_offers.agency_index_col_operator') }}</th>
                                                <th>{{ __('account.package_offers.agency_index_col_package') }}</th>
                                                <th>{{ __('account.reservations.col_travel_dates') }}</th>
                                                <th>{{ __('account.reservations.col_passengers') }}</th>
                                                <th>{{ __('account.package_offers.agency_index_col_status') }}</th>
                                                <th class="text-end">{{ __('account.package_offers.agency_index_col_price') }}</th>
                                                <th class="text-end text-nowrap">{{ __('wizard.provider_services_col_actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bookings as $booking)
                                                @php
                                                    $operatorLabel = $booking->operatorAccount?->commercial_name
                                                        ?? $booking->operatorAccount?->name
                                                        ?? ('#' . $booking->operator_id);
                                                    $packageLabel = $booking->packageOffer?->catalog?->displayLabel() ?? '—';
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $booking->booking_code }}</td>
                                                    <td>{{ $operatorLabel }}</td>
                                                    <td>{{ $packageLabel !== '' ? $packageLabel : '—' }}</td>
                                                    <td class="text-muted small">
                                                        {{ locale_date($booking->travel_start_date) }}
                                                        —
                                                        {{ locale_date($booking->travel_end_date) }}
                                                    </td>
                                                    <td class="small">
                                                        {{ \App\Support\BookingPassengersSnapshot::formatSummary($booking->passengers_snapshot) }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                            {{ $booking->status?->displayLabel() ?? '—' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end fw-medium">{{ $booking->total_formatted ?? '—' }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.reservations.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.reservations.view_action') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <livewire:account.package-offer-preview-modal />

    @include('partials.feather-livewire-refresh')

    <x-site-footer-simple />
@endsection
