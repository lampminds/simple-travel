@extends('layouts.base', ['title' => __('account.reservations.operator_show_page_title', ['code' => $booking->booking_code])])

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
                        :title="__('account.reservations.operator_show_heading', ['code' => $booking->booking_code])"
                        :instructions="__('account.reservations.operator_show_intro')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">{{ __('account.reservations.operator_col_agency') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $booking->agencyAccount?->commercial_name ?? $booking->agencyAccount?->name ?? ('#' . $booking->agency_id) }}
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
                                @if ($customerRemarks)
                                    <dt class="col-sm-4">{{ __('account.reservations.field_remarks') }}</dt>
                                    <dd class="col-sm-8">{{ $customerRemarks }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @include('account.reservations.partials.price-breakdown', ['breakdown' => $priceBreakdown])

                    @if ($canDecide)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="mb-3">{{ __('account.reservations.operator_actions_heading') }}</h6>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <form method="POST" action="{{ route('account.operator.reservations.confirm', $booking) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm(@js(__('account.reservations.operator_confirm_prompt')))">
                                            {{ __('account.reservations.operator_confirm_action') }}
                                        </button>
                                    </form>
                                </div>
                                <form method="POST" action="{{ route('account.operator.reservations.reject', $booking) }}">
                                    @csrf
                                    <label for="reason" class="form-label">{{ __('account.reservations.operator_reject_reason') }}</label>
                                    <textarea
                                        name="reason"
                                        id="reason"
                                        rows="3"
                                        class="form-control @error('reason') is-invalid @enderror"
                                        required
                                    >{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('booking')
                                        <div class="alert alert-danger mt-3 mb-0" role="alert">{{ $message }}</div>
                                    @enderror
                                    <button type="submit" class="btn btn-outline-danger mt-3" onclick="return confirm(@js(__('account.reservations.operator_reject_prompt')))">
                                        {{ __('account.reservations.operator_reject_action') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('account.operator.reservations.index') }}" class="btn btn-outline-secondary mt-3">
                        {{ __('account.reservations.operator_show_back') }}
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
