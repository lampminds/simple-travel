@php
    use Filament\Support\Icons\Heroicon;

    $currentLocale = app()->getLocale();
    $currentLanguage = $languages->first(fn ($lang) => ($lang->lmpLanguage->code ?? $lang->lmpLanguage->code2 ?? null) === $currentLocale);
    $triggerLabel = $currentLanguage ? $currentLanguage->display_name : __('filament.resources.language');
@endphp
@if ($languages->isNotEmpty())
    <x-filament::dropdown
        placement="bottom-end"
        teleport
        :attributes="(new \Illuminate\View\ComponentAttributeBag)->class(['fi-language-switcher'])"
    >
        <x-slot name="trigger">
            <button
                type="button"
                class="fi-language-switcher-trigger fi-topbar-item-btn"
                aria-label="{{ __('filament.resources.language') }}"
            >
                {{
                    \Filament\Support\generate_icon_html(
                        Heroicon::OutlinedLanguage,
                        alias: null,
                        attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'fi-topbar-item-icon'])
                    )
                }}
                <span class="fi-topbar-item-label">{{ $triggerLabel }}</span>
                {{
                    \Filament\Support\generate_icon_html(
                        Heroicon::ChevronDown,
                        alias: \Filament\View\PanelsIconAlias::TOPBAR_GROUP_TOGGLE_BUTTON,
                        attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'fi-topbar-group-toggle-icon'])
                    )
                }}
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach ($languages as $lang)
                @php
                    $code = $lang->lmpLanguage->code ?? $lang->lmpLanguage->code2 ?? null;
                    $isActive = $code === $currentLocale;
                    $localeUrl = route('filament.' . filament()->getId() . '.locale', ['language' => $lang->id]);
                @endphp
                <x-filament::dropdown.list.item
                    :href="$localeUrl"
                    :color="$isActive ? 'primary' : 'gray'"
                    tag="a"
                >
                    {{ $lang->display_name }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
@endif
