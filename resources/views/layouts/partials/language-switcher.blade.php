@php
    $currentLocale = app()->getLocale();
    $currentLanguage = $languages->first(fn ($lang) => \App\Models\Locale::primaryTagMatches($lang->locale, $currentLocale));
    $triggerLabel = $currentLanguage ? $currentLanguage->display_name : __('nav.language');
@endphp
@if ($languages->isNotEmpty())
    <li class="nav-item dropdown ms-lg-2">
        <a class="nav-link dropdown-toggle" href="#" id="navbarLanguage" role="button"
           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
           aria-label="{{ __('nav.language') }}">
            <i data-feather="globe" class="icon icon-xxs me-1 d-none d-sm-inline-block"></i>
            {{ $triggerLabel }}
        </a>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarLanguage">
            @foreach ($languages as $lang)
                @php
                    $isActive = \App\Models\Locale::primaryTagMatches($lang->locale, $currentLocale);
                    $localeUrl = route('locale', ['language' => $lang->id]);
                @endphp
                <a class="dropdown-item {{ $isActive ? 'active' : '' }}" href="{{ $localeUrl }}">
                    {{ $lang->display_name }}
                </a>
            @endforeach
        </div>
    </li>
@endif
