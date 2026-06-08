@php
    $currencyRatesChart ??= app(\App\Services\OperatorCurrencyRatesChartService::class)->build();
@endphp

<div class="row mt-4">
    <div class="col-12 col-xl-6">
        <x-operator-currency-rates-chart :chart-data="$currencyRatesChart" class="h-100" />
    </div>
</div>
