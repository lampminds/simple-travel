@php
    use App\Support\WebsiteNavigation;
    /** @var \App\Models\Menu $menu */
    /** @var int $level */
    $children = $menu->nav_children ?? collect();
    $href = WebsiteNavigation::urlForMenu($menu);
    $label = $menu->admin_label;
@endphp
@if ($level === 0)
    @if ($children->isEmpty())
        <li class="nav-item pe-3">
            <a class="nav-link" href="{{ $href }}">{{ $label }}</a>
        </li>
    @else
        <li class="nav-item dropdown pe-3">
            <a class="nav-link dropdown-toggle" href="#" id="websiteNavDd{{ $menu->id }}" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">{{ $label }}</a>
            <ul class="dropdown-menu" aria-labelledby="websiteNavDd{{ $menu->id }}">
                @foreach ($children as $child)
                    @include('layouts.partials.website-nav-menu-node', ['menu' => $child, 'level' => 1])
                @endforeach
            </ul>
        </li>
    @endif
@else
    @if ($children->isEmpty())
        <li><a class="dropdown-item" href="{{ $href }}">{{ $label }}</a></li>
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
