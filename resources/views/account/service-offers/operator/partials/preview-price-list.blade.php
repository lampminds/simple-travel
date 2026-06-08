@php
    /** @var array<string, string|null> $priceList */
    $forPdf = $forPdf ?? false;
    $wrapperClass = $wrapperClass ?? ($forPdf ? null : 'alert alert-light border mt-2 mb-0 py-2 px-3');
@endphp

<div class="{{ $forPdf ? 'preview-price-list' : $wrapperClass }}">
    <div class="@if (! $forPdf) row g-2 small @else preview-price-list-grid @endif">
        <div class="@if (! $forPdf) col-md-6 @endif">
            <div class="@if ($forPdf) preview-label @else text-muted text-uppercase fs-12 @endif">{{ __('account.service_offers.operator_preview_price_list_validity') }}</div>
            <div>{{ $priceList['list_validity'] ?? '—' }}</div>
        </div>
        <div class="@if (! $forPdf) col-md-6 @endif">
            <div class="@if ($forPdf) preview-label @else text-muted text-uppercase fs-12 @endif">{{ __('account.service_offers.operator_preview_assignment_validity') }}</div>
            <div>{{ $priceList['assignment_validity'] ?? '—' }}</div>
        </div>
    </div>
</div>
