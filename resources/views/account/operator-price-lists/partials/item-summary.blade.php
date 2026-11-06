@php
    $record = $record ?? null;
    $selectedPackageItemId = (string) ($selectedPackageItemId ?? '');
@endphp
<div data-role="package-item-summary">
    @if (is_array($record))
        <div class="fw-semibold">{{ $record['package_name'] }}</div>
        @php
            $serviceLine = trim(implode(' — ', array_filter([
                $record['sku'] ?? '',
                $record['service_name'] ?? '',
            ], static fn ($part) => $part !== '')));
        @endphp
        @if ($serviceLine !== '')
            <div>{{ $serviceLine }}</div>
        @endif
        @if (($record['variant_name'] ?? '') !== '')
            <div class="text-muted">{{ $record['variant_name'] }}</div>
        @endif
        @php
            $metaParts = array_filter([
                $record['day_label'] ?? '',
                $record['inclusion'] ?? '',
                (int) ($record['quantity'] ?? 1) > 1 ? '×'.$record['quantity'] : '',
                $record['provider'] ?? '',
            ], static fn ($part) => $part !== '');
        @endphp
        @if ($metaParts !== [])
            <div class="text-muted small">{{ implode(' · ', $metaParts) }}</div>
        @endif
        @if (($record['schedule'] ?? '') !== '')
            <div class="small">{{ __('account.operator_price_lists.picker.columns.schedule') }}: {{ $record['schedule'] }}</div>
        @endif
        <div class="small fw-semibold" data-role="package-item-summary-cost"></div>
    @elseif ($selectedPackageItemId !== '')
        <div class="fw-semibold">{{ __('account.operator_price_lists.picker.missing_line', ['id' => $selectedPackageItemId]) }}</div>
        <div class="small fw-semibold" data-role="package-item-summary-cost"></div>
    @else
        <span class="text-muted">{{ __('account.operator_price_lists.fields.package_item_placeholder') }}</span>
    @endif
</div>
