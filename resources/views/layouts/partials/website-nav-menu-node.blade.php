@php
    use App\Support\WebsiteNavigation;
    /** @var \App\Models\Menu $menu */
    /** @var int $level */
    $children = $menu->nav_children ?? collect();
    $href = WebsiteNavigation::urlForMenu($menu);
    $label = $menu->admin_label;
    $hasNavTarget = $href !== '#';
@endphp
@if ($level === 0)
    @if ($children->isEmpty())
        <li class="nav-item pe-3">
            <a class="nav-link" href="{{ $href }}">{{ $label }}</a>
        </li>
    @else
        <li class="nav-item dropdown pe-3">
            @if ($hasNavTarget)
                {{-- Below lg: one toggle opens children; parent URL is the first submenu row (desktop keeps split link + caret). --}}
                <a class="nav-link dropdown-toggle d-lg-none w-100 text-center" href="#"
                   id="websiteNavDdMob{{ $menu->id }}" role="button"
                   data-bs-toggle="dropdown" data-bs-target="#websiteNavMenu{{ $menu->id }}"
                   aria-controls="websiteNavMenu{{ $menu->id }}" aria-expanded="false">{{ $label }}</a>
                <div class="website-nav-split d-none d-lg-flex align-items-center">
                    <a class="nav-link pe-1" href="{{ $href }}">{{ $label }}</a>
                    <a class="nav-link dropdown-toggle ps-1" href="#" id="websiteNavDd{{ $menu->id }}"
                       role="button" data-bs-toggle="dropdown" data-bs-target="#websiteNavMenu{{ $menu->id }}"
                       aria-controls="websiteNavMenu{{ $menu->id }}" aria-expanded="false"
                       aria-label="{{ __('nav.open_submenu_for', ['label' => $label]) }}"></a>
                </div>
                <ul id="websiteNavMenu{{ $menu->id }}" class="dropdown-menu"
                    aria-labelledby="websiteNavDdMob{{ $menu->id }} websiteNavDd{{ $menu->id }}">
                    <li class="d-lg-none">
                        <a class="dropdown-item fw-medium" href="{{ $href }}">{{ __('nav.link_to_parent', ['label' => $label]) }}</a>
                    </li>
                    @foreach ($children as $child)
                        @include('layouts.partials.website-nav-menu-node', ['menu' => $child, 'level' => 1])
                    @endforeach
                </ul>
            @else
                <a class="nav-link dropdown-toggle" href="#" id="websiteNavDd{{ $menu->id }}" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">{{ $label }}</a>
                <ul class="dropdown-menu" aria-labelledby="websiteNavDd{{ $menu->id }}">
                    @foreach ($children as $child)
                        @include('layouts.partials.website-nav-menu-node', ['menu' => $child, 'level' => 1])
                    @endforeach
                </ul>
            @endif
        </li>
    @endif
@else
    @if ($children->isEmpty())
        <li><a class="dropdown-item" href="{{ $href }}">{{ $label }}</a></li>
    @else
        @if ($hasNavTarget)
            <li class="dropend">
                <a class="dropdown-item dropdown-toggle d-lg-none" href="#"
                   id="websiteNavSubMob{{ $menu->id }}" role="button"
                   data-bs-toggle="dropdown" data-bs-target="#websiteNavSubMenu{{ $menu->id }}"
                   aria-controls="websiteNavSubMenu{{ $menu->id }}" aria-expanded="false">{{ $label }}</a>
                <div class="d-none d-lg-flex align-items-stretch w-100">
                    <a class="dropdown-item flex-grow-1 align-self-center" href="{{ $href }}">{{ $label }}</a>
                    <a class="dropdown-item dropdown-toggle w-auto align-self-center py-2 px-2" href="#"
                       id="websiteNavSub{{ $menu->id }}" role="button" data-bs-toggle="dropdown"
                       data-bs-target="#websiteNavSubMenu{{ $menu->id }}"
                       aria-controls="websiteNavSubMenu{{ $menu->id }}" aria-expanded="false"
                       aria-label="{{ __('nav.open_submenu_for', ['label' => $label]) }}"></a>
                </div>
                <ul id="websiteNavSubMenu{{ $menu->id }}" class="dropdown-menu"
                    aria-labelledby="websiteNavSubMob{{ $menu->id }} websiteNavSub{{ $menu->id }}">
                    <li class="d-lg-none">
                        <a class="dropdown-item fw-medium" href="{{ $href }}">{{ __('nav.link_to_parent', ['label' => $label]) }}</a>
                    </li>
                    @foreach ($children as $child)
                        @include('layouts.partials.website-nav-menu-node', ['menu' => $child, 'level' => $level + 1])
                    @endforeach
                </ul>
            </li>
        @else
            <li class="dropend">
                <a class="dropdown-item dropdown-toggle" href="#" id="websiteNavSub{{ $menu->id }}" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">{{ $label }}</a>
                <ul class="dropdown-menu" aria-labelledby="websiteNavSub{{ $menu->id }}">
                    @foreach ($children as $child)
                        @include('layouts.partials.website-nav-menu-node', ['menu' => $child, 'level' => $level + 1])
                    @endforeach
                </ul>
            </li>
        @endif
    @endif
@endif
