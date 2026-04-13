@php
    use App\Filament\Resources\MenuResource;
    /** @var \App\Models\Menu $menu */
@endphp

<li wire:key="menu-node-{{ $menu->id }}" class="list-none">
    <div class="flex flex-wrap items-center gap-2 py-1">
        <div class="inline-flex shrink-0 gap-0.5">
            <x-filament::icon-button
                :icon="'heroicon-m-arrow-up'"
                color="gray"
                size="sm"
                :label="__('filament.pages.website_menu_editor.move_up')"
                wire:click="moveMenu({{ $menu->id }}, 'up')"
            />
            <x-filament::icon-button
                :icon="'heroicon-m-arrow-down'"
                color="gray"
                size="sm"
                :label="__('filament.pages.website_menu_editor.move_down')"
                wire:click="moveMenu({{ $menu->id }}, 'down')"
            />
        </div>
        <x-filament::badge :color="$menu->active ? 'success' : 'gray'">
            {{ $menu->active ? __('filament.pages.website_menu_editor.active') : __('filament.pages.website_menu_editor.inactive') }}
        </x-filament::badge>
        <a
            href="{{ MenuResource::getUrl('edit', ['record' => $menu]) }}"
            class="text-primary-600 hover:underline font-medium text-sm dark:text-primary-400"
        >
            {{ $menu->admin_label }}
        </a>
        <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $menu->slug }}</span>
        @if (filled($menu->route))
            <span class="text-xs text-gray-400 dark:text-gray-500">· {{ $menu->route }}</span>
        @endif
    </div>
    @if ($menu->relationLoaded('children') && $menu->children->isNotEmpty())
        <ul class="mt-1 ml-4 space-y-1 border-l border-gray-200 pl-3 dark:border-white/10">
            @foreach ($menu->children as $child)
                @include('filament.pages.partials.website-menu-branch', ['menu' => $child])
            @endforeach
        </ul>
    @endif
</li>
