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
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.service_offers.operator_index_heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">{{ __('account.service_offers.operator_index_intro') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($offers->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.service_offers.operator_index_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.service_offers.operator_index_col_provider') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_service') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_variant') }}</th>
                                                <th>{{ __('account.service_offers.operator_index_col_offered') }}</th>
                                                <th class="text-end text-nowrap">{{ __('wizard.provider_services_col_actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($offers as $offer)
                                                @php
                                                    $v = $offer->serviceVariant;
                                                    $svc = $v?->service;
                                                    $providerLabel = $offer->providerAccount?->commercial_name
                                                        ?? $offer->providerAccount?->name
                                                        ?? ('#' . $offer->provider_id);
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $providerLabel }}</td>
                                                    <td>{{ $svc && $svc->name !== '' ? $svc->name : ('—') }}</td>
                                                    <td>{{ $v && $v->name !== '' ? $v->name : ($v?->sku ?? '—') }}</td>
                                                    <td>{{ $offer->offered_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                                    <td class="text-end text-nowrap">
                                                        <form method="POST" action="{{ route('account.service-offers.accept', $offer) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary me-1">{{ __('account.service_offers.operator_index_accept') }}</button>
                                                        </form>
                                                        <form
                                                            method="POST"
                                                            action="{{ route('account.service-offers.reject', $offer) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm(@js(__('account.service_offers.operator_index_reject_confirm')))"
                                                        >
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.service_offers.operator_index_reject') }}</button>
                                                        </form>
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
