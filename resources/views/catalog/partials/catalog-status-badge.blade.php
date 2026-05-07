{{--
    Expects: $presentation — array{ badge: string, icon: ?string, label: string } from ServiceCatalogStatus::forService / forVariant
--}}
@php
    $presentation = $presentation ?? ['badge' => 'light', 'icon' => null, 'label' => '—'];
@endphp
<span class="badge text-bg-{{ $presentation['badge'] }} d-inline-flex align-items-center gap-1">
    @if (! empty($presentation['icon']))
        <i class="icon icon-xxs" data-feather="{{ $presentation['icon'] }}" aria-hidden="true"></i>
    @endif
    {{ $presentation['label'] }}
</span>
