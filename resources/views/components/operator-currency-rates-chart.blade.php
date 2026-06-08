@props(['chartData'])

@php
    $payload = is_array($chartData) ? $chartData : [];
    $currencies = $payload['currencies'] ?? [];
    $defaultId = $payload['default_currency_id'] ?? null;
    $strings = $payload['strings'] ?? [];
    $hasData = $currencies !== [];
@endphp

<div
    {{ $attributes->class('card border-0 shadow-sm operator-currency-rates-chart') }}
    @if ($hasData)
        id="operator-currency-rates-chart-root"
        data-chart="{{ json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    @endif
>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1 fw-semibold">{{ $strings['heading'] ?? '' }}</h2>
                <p class="text-muted small mb-0" data-chart-updated>
                    @if ($hasData)
                        {{ $strings['updated'] ?? '' }}
                        <span data-chart-updated-value>—</span>
                    @endif
                </p>
            </div>

            @if ($hasData)
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <label for="operator-currency-chart-select" class="form-label mb-0 small text-muted">
                        {{ __('exchange_rates.columns.currency') }}
                    </label>
                    <select
                        id="operator-currency-chart-select"
                        class="form-select form-select-sm"
                        style="min-width: 12rem;"
                        data-chart-currency-select
                    >
                        @foreach ($currencies as $currency)
                            <option
                                value="{{ $currency['id'] }}"
                                @selected((int) $currency['id'] === (int) $defaultId)
                            >
                                {{ $currency['code'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @if ($hasData)
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <div class="border rounded p-3 h-100 bg-light-subtle">
                        <p class="text-uppercase text-muted small mb-1">{{ $strings['buy'] ?? '' }}</p>
                        <p class="h4 mb-0 font-monospace" data-chart-current-buy>—</p>
                        <p class="text-muted small mb-0">{{ $strings['per_usd'] ?? '' }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="border rounded p-3 h-100 bg-light-subtle">
                        <p class="text-uppercase text-muted small mb-1">{{ $strings['sell'] ?? '' }}</p>
                        <p class="h4 mb-0 font-monospace" data-chart-current-sell>—</p>
                        <p class="text-muted small mb-0">{{ $strings['per_usd'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div style="position: relative; height: 220px; max-width: 100%;">
                <canvas data-chart-canvas aria-label="{{ $strings['heading'] ?? '' }}"></canvas>
            </div>
        @else
            <p class="text-muted mb-0">{{ $strings['empty'] ?? '' }}</p>
        @endif
    </div>
</div>
