@extends('layouts.base', ['title' => __('account.service_offers.provider_edit_page_title', ['operator' => $operator->commercial_name ?? $operator->name ?? $operator->nick])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.service_offers.provider_edit_title')"
                        :subtitle="$operator->commercial_name ?? $operator->name ?? $operator->nick"
                        :instructions="__('account.service_offers.provider_edit_intro')"
                    />
                </div>
            </div>

            @if ($serviceStatusOptions->count() > 1)
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <form method="get" action="{{ route('account.service-offers.operators.edit', $operator) }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
                            <div>
                                <label for="service_status" class="form-label small mb-1">{{ __('account.service_offers.provider_edit_filter_service_status_label') }}</label>
                                <select name="service_status" id="service_status" class="form-select form-select-sm" style="min-width: 14rem;" onchange="this.form.submit()">
                                    <option value="">{{ __('account.service_offers.provider_edit_filter_service_status_all') }}</option>
                                    @foreach ($serviceStatusOptions as $st)
                                        @php $stLabel = \App\Support\ServiceCatalogStatus::forService($st)['label']; @endphp
                                        <option value="{{ $st }}" @selected($serviceStatusFilter === $st)>{{ $stLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-lg-12">
                    <a href="{{ route('account.service-offers.index', ['as' => 'provider']) }}" class="btn btn-sm btn-outline-secondary mb-3">
                        {{ __('account.service_offers.provider_edit_cancel') }}
                    </a>
                </div>
            </div>

            <form id="provider-offer-proposals-form" method="POST" action="{{ route('account.service-offers.operators.update', $operator) }}">
                @csrf
                @method('PUT')
                @if ($serviceStatusFilter !== '')
                    <input type="hidden" name="service_status" value="{{ $serviceStatusFilter }}">
                @endif

                @if ($services->isEmpty())
                    <div class="alert alert-light border">{{ __('account.service_offers.provider_edit_empty_services') }}</div>
                @else
                    @foreach ($services as $service)
                        @php
                            $serviceCatalogSelectable = $service->catalogSelectableForOperatorOffers();
                            $svcCatalog = \App\Support\ServiceCatalogStatus::forService($service->status);
                        @endphp
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">
                                {{ $service->name !== '' ? $service->name : ('Service #' . $service->id) }}
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('account.service_offers.provider_edit_col_variant') }}</th>
                                                <th>{{ __('account.service_offers.provider_edit_col_sku') }}</th>
                                                <th>{{ __('account.service_offers.provider_edit_col_catalog_status') }}</th>
                                                <th>{{ __('account.service_offers.provider_edit_col_offer_state') }}</th>
                                                <th>{{ __('account.service_offers.provider_edit_col_price_list') }}</th>
                                                <th class="text-end">{{ __('account.service_offers.provider_edit_col_operator_price') }}</th>
                                                <th class="text-center">{{ __('account.service_offers.provider_edit_col_propose') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($service->serviceVariants as $variant)
                                                @php
                                                    $status = $variant->offer_status ?? 'none';
                                                    $catalogSelectable = $variant->catalogSelectableForOperatorOffers($service);
                                                    $svcCatalog = \App\Support\ServiceCatalogStatus::forService($service->status);
                                                    $varCatalog = \App\Support\ServiceCatalogStatus::forVariant($variant->status);
                                                    $opPrice = $variant->operator_price ?? [
                                                        'has_amount' => false,
                                                        'formatted' => '—',
                                                        'breakdown_html' => '<div class="price-breakdown-popover text-start small"><div>—</div></div>',
                                                    ];
                                                    $hasOperatorPriceList = (bool) ($variant->operator_has_price_list ?? $operatorHasPriceList ?? false);
                                                    $operatorPriceListLabel = (string) ($variant->operator_price_list_name ?? $operatorPriceListName ?? '');
                                                    $operatorPriceIsZero = (bool) ($variant->operator_price_is_zero ?? false);
                                                    $proposeSelectable = $catalogSelectable && ! $operatorPriceIsZero;
                                                    $bdId = 'operator-price-bd-' . $variant->id;
                                                @endphp
                                                <tr>
                                                    <td>{{ $variant->name !== '' ? $variant->name : '—' }}</td>
                                                    <td><code class="small">{{ $variant->sku }}</code></td>
                                                    <td class="small">
                                                        <div>
                                                            <span class="text-muted me-1">{{ __('account.service_offers.provider_edit_catalog_service_prefix') }}</span>
                                                            <span class="badge text-bg-{{ $svcCatalog['badge'] }}">{{ $svcCatalog['label'] }}</span>
                                                        </div>
                                                        <div class="mt-1">
                                                            <span class="text-muted me-1">{{ __('account.service_offers.provider_edit_catalog_variant_prefix') }}</span>
                                                            <span class="badge text-bg-{{ $varCatalog['badge'] }}">{{ $varCatalog['label'] }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                            {{ __('account.service_offers.provider_edit_state_' . $status) }}
                                                        </span>
                                                        @if ($status === 'accepted')
                                                            <div class="small text-muted mt-1">{{ __('account.service_offers.provider_edit_accepted_note') }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($hasOperatorPriceList && $operatorPriceListLabel !== '')
                                                            {{ $operatorPriceListLabel }}
                                                        @else
                                                            <span class="text-danger">{{ __('account.service_offers.provider_edit_price_list_none') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <button
                                                            type="button"
                                                            class="btn btn-link p-0 border-0 text-end text-decoration-underline d-inline-block @if ($opPrice['has_amount']) fw-medium text-body @else text-muted @endif js-operator-price-popover"
                                                            data-bs-placement="left"
                                                            data-operator-price-popover="{{ $bdId }}"
                                                            aria-label="{{ __('account.service_offers.provider_edit_price_breakdown_aria') }}"
                                                        >{{ $opPrice['formatted'] }}</button>
                                                        <div id="{{ $bdId }}" class="d-none">{!! $opPrice['breakdown_html'] !!}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($status === 'accepted')
                                                            <span class="text-muted">—</span>
                                                        @elseif ($status === 'pending')
                                                            <input
                                                                type="checkbox"
                                                                class="form-check-input"
                                                                checked
                                                                disabled
                                                                title="{{ __('account.service_offers.provider_edit_pending_locked_hint') }}"
                                                            >
                                                            @if ($variant->offer_id)
                                                                <button
                                                                    type="submit"
                                                                    form="revoke-offer-{{ $variant->offer_id }}"
                                                                    class="btn btn-sm btn-outline-danger mt-1 d-block mx-auto"
                                                                    onclick="return confirm(@js(__('account.service_offers.provider_edit_revoke_confirm')))"
                                                                >
                                                                    {{ __('account.service_offers.provider_edit_revoke') }}
                                                                </button>
                                                            @endif
                                                        @else
                                                            <input
                                                                type="checkbox"
                                                                class="form-check-input js-propose-variant"
                                                                @if ($proposeSelectable) name="propose[]" @endif
                                                                value="{{ $variant->id }}"
                                                                data-has-price-list="{{ $hasOperatorPriceList ? '1' : '0' }}"
                                                                data-variant-label="{{ $variant->sku }}"
                                                                @disabled(! $proposeSelectable)
                                                                @if (! $catalogSelectable)
                                                                    title="{{ __('account.service_offers.provider_edit_catalog_selectable_hint') }}"
                                                                @elseif ($operatorPriceIsZero)
                                                                    title="{{ __('account.service_offers.provider_edit_zero_price_hint') }}"
                                                                @endif
                                                            >
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('account.service_offers.provider_edit_save') }}</button>
                </div>
            </form>

            @foreach ($services as $service)
                @foreach ($service->serviceVariants as $variant)
                    @if (($variant->offer_status ?? 'none') === 'pending' && $variant->offer_id)
                        <form
                            id="revoke-offer-{{ $variant->offer_id }}"
                            method="POST"
                            action="{{ route('account.service-offers.operators.revoke', ['operator' => $operator, 'offer' => $variant->offer_id]) }}"
                            class="d-none"
                        >
                            @csrf
                            @if ($serviceStatusFilter !== '')
                                <input type="hidden" name="service_status" value="{{ $serviceStatusFilter }}">
                            @endif
                        </form>
                    @endif
                @endforeach
            @endforeach
        </div>
    </section>

    <x-site-footer-simple />

@endsection

{{-- Runs after @vite(theme.js): content scripts execute before Bootstrap is on window. --}}
@section('script-bottom')
    <style>
        .operator-price-breakdown-popover .popover-body {
            max-width: 24rem;
        }
    </style>
    <script>
        window.addEventListener('load', function () {
            var proposalsForm = document.getElementById('provider-offer-proposals-form');
            if (proposalsForm) {
                var noPriceListConfirmTemplate = @js(__('account.service_offers.provider_edit_submit_no_price_list_confirm'));

                proposalsForm.addEventListener('submit', function (event) {
                    var missingLabels = [];
                    proposalsForm.querySelectorAll('.js-propose-variant:checked').forEach(function (checkbox) {
                        if (checkbox.getAttribute('data-has-price-list') !== '1') {
                            missingLabels.push(checkbox.getAttribute('data-variant-label') || '—');
                        }
                    });

                    if (missingLabels.length === 0) {
                        return;
                    }

                    var message = noPriceListConfirmTemplate.replace(':variants', missingLabels.join(', '));
                    if (! window.confirm(message)) {
                        event.preventDefault();
                    }
                });
            }

            if (typeof window.bootstrap === 'undefined' || !window.bootstrap.Popover) {
                return;
            }
            document.querySelectorAll('.js-operator-price-popover').forEach(function (trigger) {
                if (trigger.getAttribute('data-operator-price-bound') === '1') {
                    return;
                }
                var targetId = trigger.getAttribute('data-operator-price-popover');
                var contentEl = targetId ? document.getElementById(targetId) : null;
                if (!contentEl) {
                    return;
                }
                trigger.setAttribute('data-operator-price-bound', '1');
                new bootstrap.Popover(trigger, {
                    html: true,
                    sanitize: false,
                    content: contentEl.innerHTML,
                    container: 'body',
                    customClass: 'operator-price-breakdown-popover',
                    trigger: 'hover focus',
                });
            });
        });
    </script>
@endsection
