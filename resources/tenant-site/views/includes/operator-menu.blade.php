@php
    $menuAccount = tenant_website_account();
    $menuCategories = $menuAccount
        ? app(\App\Services\OperatorPublicProductCategories::class)->forAccount($menuAccount)
        : collect();
@endphp
@foreach ($menuCategories as $category)
    @php
        $categoryLabel = $category->name !== '' ? $category->name : $category->code;
        $categoryIsCurrent = request()->routeIs('site.services.category')
            && (string) request()->route('code') === (string) $category->code;
    @endphp
    <li>
        <a href="{{ route('site.services.category', ['code' => $category->code]) }}" class="sub-menu-item @if ($categoryIsCurrent) menu-current @endif">{{ $categoryLabel }}</a>
    </li>
@endforeach
<li>
    <a href="{{ url('/contact') }}" class="sub-menu-item @if (request()->is('contact')) menu-current @endif">{{ __('tenant.menu_contact') }}</a>
</li>
