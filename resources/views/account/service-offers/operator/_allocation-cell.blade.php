@php
    $allocationSummary = $allocationSummary ?? ['current' => null, 'total_count' => 0];
    $currentAllocation = $allocationSummary['current'] ?? null;
    $totalCount = (int) ($allocationSummary['total_count'] ?? 0);
    $additionalCount = $currentAllocation instanceof \App\Models\Allocation
        ? max(0, $totalCount - 1)
        : $totalCount;
@endphp

@if ($currentAllocation instanceof \App\Models\Allocation)
    <div>{{ __('account.allocations.types.' . $currentAllocation->allocation_type) }}</div>
    @if ($currentAllocation->allocation_type === \App\Models\Allocation::TYPE_FREE_SALE)
        <div class="small text-muted">{{ __('account.service_offers.operator_index_allocation_unlimited') }}</div>
    @else
        <div class="fw-medium">{{ number_format((int) $currentAllocation->capacity) }}</div>
    @endif
    <div class="small text-muted mt-1">
        @if ($currentAllocation->start_date || $currentAllocation->end_date)
            {{ locale_date_range(
                $currentAllocation->start_date,
                $currentAllocation->end_date,
                null,
                __('account.allocations.validity_open'),
            ) }}
        @else
            {{ __('account.allocations.validity_open') }}
        @endif
    </div>
    @if ($additionalCount > 0)
        <div class="small text-muted mt-1">
            {{ __('account.service_offers.operator_index_allocation_more', ['count' => $additionalCount]) }}
        </div>
    @endif
@elseif ($totalCount > 0)
    <span class="text-muted">{{ __('account.service_offers.operator_index_allocation_inactive') }}</span>
    <div class="small text-muted mt-1">
        {{ trans_choice('account.service_offers.operator_index_allocation_count', $totalCount, ['count' => $totalCount]) }}
    </div>
@else
    <span class="text-muted">—</span>
@endif
