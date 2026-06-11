@extends('layouts.base', ['title' => __('account.reservations.operator_index_page_title')])

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
                        :title="__('account.reservations.operator_index_heading')"
                        :instructions="__('account.reservations.operator_index_intro')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <form method="get" action="{{ route('account.operator.reservations.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
                        <div>
                            <label for="booking_status" class="form-label small mb-1">{{ __('account.reservations.operator_filter_label') }}</label>
                            <select name="status" id="booking_status" class="form-select form-select-sm" style="min-width: 14rem;" onchange="this.form.submit()">
                                @foreach ($statusFilterOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($bookings->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.reservations.operator_index_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.reservations.col_code') }}</th>
                                                <th>{{ __('account.reservations.operator_col_agency') }}</th>
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
                                                    $agencyLabel = $booking->agencyAccount?->commercial_name
                                                        ?? $booking->agencyAccount?->name
                                                        ?? ('#' . $booking->agency_id);
                                                    $packageLabel = $booking->packageOffer?->catalog?->displayLabel() ?? '—';
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $booking->booking_code }}</td>
                                                    <td>{{ $agencyLabel }}</td>
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
                                                        <a href="{{ route('account.operator.reservations.show', $booking) }}" class="btn btn-sm btn-outline-primary">
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

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    @include('partials.feather-livewire-refresh')
@endsection
