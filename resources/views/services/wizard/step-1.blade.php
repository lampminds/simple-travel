@extends('layouts.base', ['title' => ($service ?? null) ? __('wizard.step1_title_edit') : __('wizard.step1_title_new')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @php
        $isEdit = ($service ?? null) !== null;
    @endphp

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ $isEdit ? __('wizard.step1_title_edit') : __('wizard.step1_title_new') }}</h3>
                        <p class="mt-1 fw-medium">Datos base del servicio</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mt-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form
                                method="POST"
                                action="{{ $isEdit ? route('services.wizard.step1.update', ['serviceType' => $serviceType->code, 'service' => $service->id]) : route('services.wizard.step1.store', ['serviceType' => $serviceType->code]) }}"
                            >
                                @csrf
                                @if ($isEdit)
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de servicio</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $serviceType->name ?: strtoupper($serviceType->code) }}"
                                                disabled
                                            >
                                            <small class="text-muted">Este tipo es fijo para este flujo.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="city_search" class="form-label">Ciudad del servicio <small>*</small></label>
                                            <input
                                                type="text"
                                                id="city_search"
                                                class="form-control @error('city_name') is-invalid @enderror @error('city_id') is-invalid @enderror"
                                                placeholder="Escribe al menos 4 letras para buscar…"
                                                autocomplete="off"
                                                value="{{ old('city_name', $cityDisplayLabel ?? '') }}"
                                            >
                                            <small id="city-search-hint" class="form-text text-muted">La búsqueda empieza con 4 caracteres (hay muchas ciudades).</small>
                                            <input type="hidden" id="city_id" name="city_id" value="{{ old('city_id', $isEdit ? $service->city_id : '') }}">
                                            <input type="hidden" id="city_name" name="city_name" value="{{ old('city_name', $cityDisplayLabel ?? '') }}">
                                            <div id="city-results" class="list-group mt-2 overflow-auto" style="max-height: 22rem;"></div>
                                            @error('city_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('city_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <h5 class="mb-3">Traducciones del servicio</h5>

                                <div class="row">
                                    @foreach ($languages as $language)
                                        <div class="col-lg-6">
                                            <div class="border rounded p-3 mb-3 bg-white">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="mb-0">{{ $language->display_name }}</h6>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary translate-from-language-btn"
                                                        data-source-language-id="{{ $language->id }}"
                                                        title="Traducir desde {{ $language->display_name }}"
                                                    >
                                                        <span aria-hidden="true">🌐</span>
                                                    </button>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="name_{{ $language->id }}">Nombre <small>*</small></label>
                                                    <input
                                                        type="text"
                                                        id="name_{{ $language->id }}"
                                                        name="translations[{{ $language->id }}][name]"
                                                        data-language-id="{{ $language->id }}"
                                                        class="form-control @error("translations.{$language->id}.name") is-invalid @enderror"
                                                        value="{{ old("translations.{$language->id}.name", $service ? $service->translations->firstWhere('language_id', $language->id)?->name : null) }}"
                                                    >
                                                    @error("translations.{$language->id}.name")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label" for="description_{{ $language->id }}">Descripción</label>
                                                    <textarea
                                                        id="description_{{ $language->id }}"
                                                        name="translations[{{ $language->id }}][description]"
                                                        data-language-id="{{ $language->id }}"
                                                        class="form-control @error("translations.{$language->id}.description") is-invalid @enderror"
                                                        rows="4"
                                                    >{{ old("translations.{$language->id}.description", $service ? $service->translations->firstWhere('language_id', $language->id)?->description : null) }}</textarea>
                                                    @error("translations.{$language->id}.description")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                                    <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary">@lang('wizard.nav_dashboard')</a>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        @if ($isEdit)
                                            <a
                                                href="{{ route('services.wizard.step2', ['serviceType' => $serviceType->code, 'service' => $service->id]) }}"
                                                class="btn btn-outline-primary"
                                            >@lang('wizard.nav_to_step2')</a>
                                            <a
                                                href="{{ route('services.wizard.step3', ['serviceType' => $serviceType->code, 'service' => $service->id]) }}"
                                                class="btn btn-outline-primary"
                                            >@lang('wizard.nav_to_step3')</a>
                                            <a
                                                href="{{ route('services.wizard.step4', ['serviceType' => $serviceType->code, 'service' => $service->id]) }}"
                                                class="btn btn-outline-primary"
                                            >@lang('wizard.nav_to_step4')</a>
                                        @endif
                                        <button type="submit" class="btn btn-primary">
                                            {{ $isEdit ? __('wizard.step1_submit_edit') : __('wizard.step1_submit_create') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    <script>
        (function () {
            const searchInput = document.getElementById('city_search');
            const cityIdInput = document.getElementById('city_id');
            const cityNameInput = document.getElementById('city_name');
            const resultsBox = document.getElementById('city-results');
            const citySearchHint = document.getElementById('city-search-hint');
            const MIN_CITY_QUERY_LEN = 4;
            const translateButtons = Array.from(document.querySelectorAll('.translate-from-language-btn'));
            const descriptionFields = Array.from(document.querySelectorAll('textarea[name^="translations"][name$="[description]"]'));
            const nameFields = Array.from(document.querySelectorAll('input[name^="translations"][name$="[name]"]'));
            const defaultButtonMarkup = '<span aria-hidden="true">🌐</span>';

            if (!searchInput || !cityIdInput || !cityNameInput || !resultsBox) {
                return;
            }

            let currentAbortController = null;
            let debounceTimer = null;

            function clearResults() {
                resultsBox.innerHTML = '';
            }

            function selectCity(city) {
                cityIdInput.value = city.id;
                const display = city.label || city.name;
                cityNameInput.value = display;
                searchInput.value = display;
                clearResults();
            }

            function renderResults(cities, options) {
                const opts = options || {};
                clearResults();

                if (opts.message) {
                    const note = document.createElement('div');
                    note.className = 'list-group-item text-muted small';
                    note.textContent = opts.message;
                    resultsBox.appendChild(note);
                }

                if (!cities.length) {
                    return;
                }

                cities.forEach((city) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.textContent = city.label || city.name;
                    button.addEventListener('click', () => selectCity(city));
                    resultsBox.appendChild(button);
                });
            }

            async function searchCities(query) {
                if (currentAbortController) {
                    currentAbortController.abort();
                }

                currentAbortController = new AbortController();

                const response = await fetch(`{{ route('services.cities.search') }}?q=${encodeURIComponent(query)}`, {
                    signal: currentAbortController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return { results: [], truncated: false };
                }

                const data = await response.json();
                if (Array.isArray(data)) {
                    return { results: data, truncated: false };
                }
                return {
                    results: Array.isArray(data.results) ? data.results : [],
                    truncated: Boolean(data.truncated),
                };
            }

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();

                cityIdInput.value = '';
                cityNameInput.value = query;

                if (debounceTimer) {
                    clearTimeout(debounceTimer);
                }

                if (query.length < MIN_CITY_QUERY_LEN) {
                    clearResults();
                    if (citySearchHint) {
                        citySearchHint.classList.remove('d-none');
                    }
                    return;
                }

                if (citySearchHint) {
                    citySearchHint.classList.add('d-none');
                }

                debounceTimer = setTimeout(async () => {
                    try {
                        const { results: cities, truncated } = await searchCities(query);
                        renderResults(cities, {
                            message: truncated
                                ? 'Se muestran las primeras ' + cities.length + ' coincidencias; escribe más letras para acotar.'
                                : null,
                        });
                    } catch (error) {
                        clearResults();
                    }
                }, 250);
            });

            document.addEventListener('click', (event) => {
                if (!resultsBox.contains(event.target) && event.target !== searchInput) {
                    clearResults();
                }
            });

            function collectTranslationsPayload() {
                const payload = {};

                nameFields.forEach((field) => {
                    const languageId = field.dataset.languageId;
                    if (!payload[languageId]) {
                        payload[languageId] = {};
                    }
                    payload[languageId].name = field.value || '';
                });

                descriptionFields.forEach((field) => {
                    const languageId = field.dataset.languageId;
                    if (!payload[languageId]) {
                        payload[languageId] = {};
                    }
                    payload[languageId].description = field.value || '';
                });

                return payload;
            }

            async function translateFromLanguage(sourceLanguageId, triggerButton) {
                if (!sourceLanguageId) {
                    return;
                }

                const translationsPayload = collectTranslationsPayload();
                const sourceData = translationsPayload[sourceLanguageId] || {};
                const sourceName = (sourceData.name || '').trim();
                const sourceDescription = (sourceData.description || '').trim();

                if (!sourceName && !sourceDescription) {
                    alert('Primero completa nombre o descripción en el idioma origen.');
                    return;
                }

                const allButtons = translateButtons;
                allButtons.forEach((button) => button.disabled = true);
                if (triggerButton) {
                    triggerButton.classList.add('disabled');
                    triggerButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                }

                try {
                    const response = await fetch(`{{ route('services.wizard.translate-descriptions') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            source_language_id: Number(sourceLanguageId),
                            translations: translationsPayload,
                        }),
                    });

                    if (!response.ok) {
                        let message = `Error HTTP ${response.status}`;
                        try {
                            const errorPayload = await response.json();
                            if (errorPayload.message) {
                                message = errorPayload.message;
                            } else if (errorPayload.errors) {
                                const firstError = Object.values(errorPayload.errors)[0];
                                if (Array.isArray(firstError) && firstError.length) {
                                    message = firstError[0];
                                }
                            }
                        } catch (parseError) {
                            // Non-JSON responses (for example, CSRF/session HTML page) keep HTTP status message.
                        }
                        throw new Error(message);
                    }

                    const payload = await response.json();
                    const translated = payload.translations || {};

                    nameFields.forEach((field) => {
                        const langId = field.dataset.languageId;
                        if (translated[langId] && typeof translated[langId].name === 'string') {
                            field.value = translated[langId].name;
                        }
                    });

                    descriptionFields.forEach((field) => {
                        const langId = field.dataset.languageId;
                        if (translated[langId] && typeof translated[langId].description === 'string') {
                            field.value = translated[langId].description;
                        }
                    });
                } catch (error) {
                    alert(`No se pudo traducir: ${error.message}`);
                } finally {
                    allButtons.forEach((button) => button.disabled = false);
                    if (triggerButton) {
                        triggerButton.classList.remove('disabled');
                        triggerButton.innerHTML = defaultButtonMarkup;
                    }
                }
            }

            translateButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    translateFromLanguage(button.dataset.sourceLanguageId, button);
                });
            });
        })();
    </script>
@endsection

