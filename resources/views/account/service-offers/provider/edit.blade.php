@extends('layouts.base', ['title' => __('account.service_offers.provider_edit_page_title', ['operator' => $operator->commercial_name ?? $operator->name ?? $operator->nick])])

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
                        <h3 class="my-0">{{ __('account.service_offers.provider_edit_heading', ['operator' => $operator->commercial_name ?? $operator->name ?? $operator->nick]) }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">{{ __('account.service_offers.provider_edit_intro') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <a href="{{ route('account.service-offers.index', ['as' => 'provider']) }}" class="btn btn-sm btn-outline-secondary mb-3">
                        {{ __('account.service_offers.provider_edit_cancel') }}
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('account.service-offers.operators.update', $operator) }}">
                @csrf
                @method('PUT')

                @if ($services->isEmpty())
                    <div class="alert alert-light border">{{ __('wizard.variants_none') }}</div>
                @else
                    @foreach ($services as $service)
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
                                                <th>{{ __('account.service_offers.provider_edit_col_state') }}</th>
                                                <th class="text-end">{{ __('account.service_offers.provider_edit_col_operator_price') }}</th>
                                                <th class="text-center">{{ __('account.service_offers.provider_edit_col_propose') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($service->serviceVariants as $variant)
                                                @php
                                                    $status = $variant->offer_status ?? 'none';
                                                    $opPrice = $variant->operator_price ?? [
                                                        'has_amount' => false,
                                                        'formatted' => '—',
                                                        'breakdown_html' => '<div class="price-breakdown-popover text-start small"><div>—</div></div>',
                                                    ];
                                                    $bdId = 'operator-price-bd-' . $variant->id;
                                                @endphp
                                                <tr>
                                                    <td>{{ $variant->name !== '' ? $variant->name : '—' }}</td>
                                                    <td><code class="small">{{ $variant->sku }}</code></td>
                                                    <td>
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                            {{ __('account.service_offers.provider_edit_state_' . $status) }}
                                                        </span>
                                                        @if ($status === 'accepted')
                                                            <div class="small text-muted mt-1">{{ __('account.service_offers.provider_edit_accepted_note') }}</div>
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
                                                        @else
                                                            <input
                                                                type="checkbox"
                                                                class="form-check-input"
                                                                name="propose[]"
                                                                value="{{ $variant->id }}"
                                                                @checked(in_array($status, ['pending'], true))
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
