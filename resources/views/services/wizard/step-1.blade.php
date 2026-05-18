@php
    $isEdit = ($service ?? null) !== null;
    $serviceTypeLabel = $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code);
    $headerDisplayName = $isEdit
        ? ($service->name !== '' ? $service->name : __('wizard.service_unnamed'))
        : __('wizard.header_new_service_placeholder');
    $step1PageTitle = __('wizard.header_title', [
        'type' => $serviceTypeLabel,
        'name' => $headerDisplayName,
        'step' => 1,
    ]);
    $catalogServiceDescriptionHelp = \App\Support\CatalogHelperContent::htmlForQuery(
        new \App\Services\CatalogHelperQuery(
            screenCode: \App\Support\ServiceWizardHelperScreens::STEP1_SERVICE_DESCRIPTION,
            code: 'catalog_service_description',
            serviceTypeId: $serviceType->id,
            accountTypeId: $catalogHelperAccountTypeId ?? null,
        )
    );
@endphp
@extends('layouts.base', ['title' => $step1PageTitle])

@section('css')
    <style>
        .popover.catalog-helper-popover {
            max-width: min(28rem, 92vw);
            border: 1px solid rgba(15, 23, 42, 0.18);
            border-radius: 0.5rem;
            box-shadow:
                0 0 0 1px rgba(15, 23, 42, 0.06),
                0 10px 15px -3px rgba(15, 23, 42, 0.14),
                0 20px 40px -12px rgba(15, 23, 42, 0.22);
            background-color: #f1f5f9;
            overflow: hidden;
        }
        .popover.catalog-helper-popover .popover-header {
            background-color: #e2e8f0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.12);
            color: #0f172a;
            font-weight: 600;
        }
        .popover.catalog-helper-popover .popover-body {
            max-height: min(70vh, 28rem);
            overflow: auto;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .popover.catalog-helper-popover .popover-body img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @if ($isEdit)
        @include('services.wizard.partials._steps_nav', [
            'serviceType' => $serviceType,
            'service' => $service,
            'currentStep' => 1,
        ])
    @endif

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @include('services.wizard.partials._header', [
                'serviceType' => $serviceType,
                'service' => $service ?? null,
                'step' => 1,
                'subtitle' => __('wizard.step1_subtitle'),
            ])

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
                                            <label for="city_search" class="form-label required-label">Ciudad del servicio</label>
                                            <input
                                                type="text"
                                                id="city_search"
                                                class="form-control @error('city_name') is-invalid @enderror @error('city_id') is-invalid @enderror"
                                                placeholder="Escribe al menos 4 letras para buscar…"
                                                autocomplete="off"
                                                value="{{ old('city_name', $cityDisplayLabel ?? '') }}"
                                                @unless ($isEdit) autofocus @endunless
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

                                <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                    <h5 class="mb-0">{{ __('wizard.step1_service_description_heading') }}</h5>
                                    <x-catalog-helper-icon
                                        :html="$catalogServiceDescriptionHelp"
                                        trigger-id="step1-catalog-helper-service-description"
                                        content-id="step1-catalog-helper-service-description-html"
                                        :aria-label="__('wizard.catalog_helper.aria_label')"
                                    />
                                </div>

                                <div class="row">
                                    @foreach ($languages as $language)
                                        <div class="col-12 col-md-6 col-lg-4">
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
                                                    <label class="form-label required-label" for="name_{{ $language->id }}">Nombre</label>
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

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('wizard.step1_submit_edit') : __('wizard.step1_submit_create') }}
                                    </button>
                                </div>

                                @include('services.wizard.partials._footer', [
                                    'serviceType' => $serviceType,
                                    'service' => $service ?? null,
                                    'currentStep' => 1,
                                ])
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

            @unless ($isEdit)
            // Focus city search on new-service flow (navbar or other scripts may run first).
            window.requestAnimationFrame(() => {
                searchInput.focus({ preventScroll: false });
            });
            @endunless
        })();

    </script>
    @include('partials.catalog-helper-popover-script')
@endsection

