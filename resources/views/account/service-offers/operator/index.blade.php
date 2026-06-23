@extends('layouts.base', ['title' => __('account.service_offers.operator_index_page_title')])

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
                        :title="__('account.service_offers.operator_index_heading')"
                        :instructions="__('account.service_offers.operator_index_intro')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <form method="get" action="{{ route('account.service-offers.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
                        <input type="hidden" name="as" value="operator">
                        <div>
                            <label for="offer_status" class="form-label small mb-1">{{ __('account.service_offers.operator_index_filter_label') }}</label>
                            <select name="status" id="offer_status" class="form-select form-select-sm" style="min-width: 14rem;" onchange="this.form.submit()">
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
                            @if ($offers->isEmpty())
                                <p class="text-muted mb-0">
                                    @if ($statusFilter === 'accepted')
                                        {{ __('account.service_offers.operator_index_empty_accepted') }}
                                    @elseif ($statusFilter === 'all')
                                        {{ __('account.service_offers.operator_index_empty_all') }}
                                    @else
                                        {{ __('account.service_offers.operator_index_empty_pending') }}
                                    @endif
                                </p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.service_offers.operator_index_col_provider') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_service') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_status') }}</th>
                                                <th class="text-end">{{ __('account.service_offers.operator_index_col_price') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_allocation') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_offered') }}</th>
                                                <th class="text-end">{{ __('account.service_offers.operator_index_col_packages') }}</th>
                                                <th class="text-end text-nowrap">{{ __('wizard.provider_services_col_actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($offers as $offer)
                                                @php
                                                    $providerLabel = $offer->providerAccount?->commercial_name
                                                        ?? $offer->providerAccount?->name
                                                        ?? ('#' . $offer->provider_id);
                                                    $opPrice = $offer->operator_price ?? [
                                                        'has_amount' => false,
                                                        'formatted' => '—',
                                                    ];
                                                    $isPending = $offer->status === \App\Models\ServiceOffer::STATUS_PENDING;
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $providerLabel }}</td>
                                                    <td>{{ $offer->operator_service_label ?? '—' }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                            {{ __('account.service_offers.provider_edit_state_' . $offer->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end @if ($opPrice['has_amount']) fw-medium @else text-muted @endif">
                                                        <div>{{ $opPrice['formatted'] }}</div>
                                                        @if (($opPrice['usd_hint'] ?? '') !== '')
                                                            <div class="small text-muted fw-normal mt-1">{{ $opPrice['usd_hint'] }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @include('account.service-offers.operator._allocation-cell', [
                                                            'allocationSummary' => $offer->operator_allocation ?? ['current' => null, 'total_count' => 0],
                                                        ])
                                                    </td>
                                                    <td>{{ $offer->offered_at ? locale_datetime($offer->offered_at) : '—' }}</td>
                                                    <td class="text-end">{{ number_format((int) ($offer->packages_count ?? 0)) }}</td>
                                                    <td class="text-end text-nowrap">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-secondary me-1"
                                                            onclick="Livewire.dispatch('open-service-offer-preview', { offerUuid: @js($offer->uuid) })"
                                                        >
                                                            {{ __('account.service_offers.operator_index_preview') }}
                                                        </button>
                                                        @if ($isPending)
                                                            <form method="POST" action="{{ route('account.service-offers.accept', $offer) }}" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                                                                <button type="submit" class="btn btn-sm btn-primary me-1">{{ __('account.service_offers.operator_index_accept') }}</button>
                                                            </form>
                                                            <form
                                                                method="POST"
                                                                action="{{ route('account.service-offers.reject', $offer) }}"
                                                                class="d-inline"
                                                                onsubmit="return confirm(@js(__('account.service_offers.operator_index_reject_confirm')))"
                                                            >
                                                                @csrf
                                                                <input type="hidden" name="status" value="{{ $statusFilter }}">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.service_offers.operator_index_reject') }}</button>
                                                            </form>
                                                        @else
                                                            <a
                                                                href="{{ route('account.operator-package-services.index', ['service_offer' => $offer->uuid]) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                            >
                                                                {{ __('account.service_offers.operator_index_view_packages') }}
                                                            </a>
                                                        @endif
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

    <livewire:account.service-offer-preview-modal />

    @include('partials.feather-livewire-refresh')

    <x-site-footer-simple />
@endsection
