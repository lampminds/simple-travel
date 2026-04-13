<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('filament.pages.website_menu_editor.section_heading') }}
        </x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ __('filament.pages.website_menu_editor.hint') }}
        </p>
        <ul class="space-y-1 rounded-xl border border-gray-200 p-4 dark:border-white/10">
            @foreach ($this->rootMenus as $menu)
                @include('filament.pages.partials.website-menu-branch', ['menu' => $menu])
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-panels::page>
