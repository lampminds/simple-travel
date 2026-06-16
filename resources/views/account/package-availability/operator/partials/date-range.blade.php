@php
    $openEnded = __('account.availability.validity_open');
@endphp
@if ($start || $end)
    {{ locale_date_range($start, $end, null, $openEnded) }}
@else
    <span class="text-muted">{{ $openEnded }}</span>
@endif
