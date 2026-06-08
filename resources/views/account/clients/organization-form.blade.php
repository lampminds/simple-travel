@php
    $isEdit = $organization !== null;
    $errorKeys = $errors->keys();
    $hasGeneralErrors = collect($errorKeys)->contains(fn (string $key): bool => ! \Illuminate\Support\Str::startsWith($key, 'tax_ids.'));
    $hasTaxErrors = collect($errorKeys)->contains(fn (string $key): bool => \Illuminate\Support\Str::startsWith($key, 'tax_ids.'));
    $activeTab = $hasTaxErrors && ! $hasGeneralErrors ? 'tax_ids' : 'general';
    $hasCatalogCityErrors = $errors->has('city_id');
    $hasManualCityErrors = $errors->hasAny(['city', 'state', 'country_id']);

    $oldTaxIds = old('tax_ids');
    $taxRows = [];
    if (is_array($oldTaxIds)) {
        $taxRows = array_values($oldTaxIds);
    } else {
        foreach ($taxIds as $taxId) {
            $taxRows[] = [
                'id' => $taxId->id,
                'document_id' => $taxId->document_id,
                'value' => $taxId->value,
                'delete' => false,
            ];
        }
    }
    $nextTaxIndex = count($taxRows);

    $cityLocationMode = $cityLocationMode ?? 'catalog';
    if ($errors->hasAny(['city', 'country_id']) && ! $errors->has('city_id')) {
        $cityLocationMode = 'manual';
    } elseif ($errors->has('city_id')) {
        $cityLocationMode = 'catalog';
    }
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.clients.edit_organization_page_title') : __('account.clients.create_organization_page_title')])

@section('css')
    <style>
        .client-org-tabs-wrap {
            background-color: var(--bs-tertiary-bg);
            border-radius: var(--bs-border-radius);
            padding: 0.5rem 0.5rem 0;
        }
        .client-org-tabs-wrap .nav-tabs {
            border-bottom: none;
            gap: 0.25rem;
        }
        .client-org-tabs-wrap .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-radius: 0.375rem 0.375rem 0 0;
            color: var(--bs-secondary-color);
            background-color: rgba(var(--bs-emphasis-color-rgb), 0.06);
            margin-bottom: 0;
        }
        .client-org-tabs-wrap .nav-tabs .nav-link.active {
            color: var(--bs-primary);
            font-weight: 600;
            background-color: var(--bs-body-bg);
            border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-body-bg);
            box-shadow: 0 -0.2rem 0 0 var(--bs-primary);
        }
        .client-org-tabs-wrap .nav-tabs .nav-link.active.text-danger,
        .client-org-tabs-wrap .nav-tabs .nav-link.text-danger {
            color: var(--bs-danger) !important;
            box-shadow: 0 -0.2rem 0 0 var(--bs-danger);
        }
        .client-org-city-tabs .nav-link.active {
            color: var(--bs-primary);
            font-weight: 600;
            background-color: var(--bs-body-bg);
            border-color: var(--bs-border-color);
        }
    </style>
@endsection

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="$isEdit ? __('account.clients.edit_organization_heading') : __('account.clients.create_organization_heading')"
                        :subtitle="$isEdit ? $organization->displayName() : null"
                        :instructions="__('account.clients.organization_form_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-10 col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ $submitRoute }}" id="client-org-form" novalidate>
                                @csrf
                                @if ($submitMethod !== 'POST')
                                    @method($submitMethod)
                                @endif

                                @if ($errors->any())
                                    <div id="client-org-form-errors" class="alert alert-danger mb-3" role="alert" tabindex="-1">
                                        <div class="fw-semibold">{{ __('account.clients.validation.form_errors_heading') }}</div>
                                        <p class="mb-2 mt-1 small">{{ __('account.clients.validation.tabs_hint') }}</p>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="client-org-tabs-wrap mb-3">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link @if ($activeTab === 'general') active @endif @if ($hasGeneralErrors) text-danger fw-semibold @endif"
                                                type="button"
                                                data-bs-toggle="tab"
                                                data-bs-target="#client-org-tab-general"
                                                role="tab"
                                            >
                                                {{ __('account.clients.tab_general') }}
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link @if ($activeTab === 'tax_ids') active @endif @if ($hasTaxErrors) text-danger fw-semibold @endif"
                                                type="button"
                                                data-bs-toggle="tab"
                                                data-bs-target="#client-org-tab-tax"
                                                role="tab"
                                            >
                                                {{ __('account.clients.tab_tax_ids') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade @if ($activeTab === 'general') show active @endif" id="client-org-tab-general" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-org-legal-name">{{ __('account.clients.fields.legal_name') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('legal_name') is-invalid @enderror"
                                                    id="client-org-legal-name"
                                                    name="legal_name"
                                                    value="{{ old('legal_name', $organization?->legal_name) }}"
                                                    required
                                                    maxlength="255"
                                                >
                                                @error('legal_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-org-trade-name">{{ __('account.clients.fields.trade_name') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('trade_name') is-invalid @enderror"
                                                    id="client-org-trade-name"
                                                    name="trade_name"
                                                    value="{{ old('trade_name', $organization?->trade_name) }}"
                                                    maxlength="255"
                                                >
                                                @error('trade_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label" for="client-org-website">{{ __('account.clients.fields.website') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('website') is-invalid @enderror"
                                                    id="client-org-website"
                                                    name="website"
                                                    value="{{ old('website', $organization?->website) }}"
                                                    maxlength="255"
                                                    placeholder="https://ejemplo.com"
                                                    inputmode="url"
                                                    autocomplete="url"
                                                >
                                                @error('website')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <hr class="my-1">
                                                <h6 class="text-muted mb-0">{{ __('account.clients.billing_address_heading') }}</h6>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label" for="client-org-address-line1">{{ __('account.clients.fields.address_line_1') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('address_line_1') is-invalid @enderror"
                                                    id="client-org-address-line1"
                                                    name="address_line_1"
                                                    value="{{ old('address_line_1', $billingAddress?->address_line_1) }}"
                                                    required
                                                    maxlength="255"
                                                >
                                                @error('address_line_1')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label" for="client-org-address-line2">{{ __('account.clients.fields.address_line_2') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('address_line_2') is-invalid @enderror"
                                                    id="client-org-address-line2"
                                                    name="address_line_2"
                                                    value="{{ old('address_line_2', $billingAddress?->address_line_2) }}"
                                                    maxlength="255"
                                                >
                                                @error('address_line_2')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label d-block">{{ __('account.clients.fields.city') }}</label>
                                                <input type="hidden" name="city_location_mode" id="client-org-city-mode" value="{{ $cityLocationMode }}">

                                                <ul class="nav nav-pills client-org-city-tabs mb-3" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button
                                                            class="nav-link @if ($cityLocationMode === 'catalog') active @endif @if ($hasCatalogCityErrors) text-danger fw-semibold @endif"
                                                            type="button"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#client-org-city-tab-catalog"
                                                            data-city-mode="catalog"
                                                            role="tab"
                                                        >
                                                            {{ __('account.clients.tab_city_catalog') }}
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button
                                                            class="nav-link @if ($cityLocationMode === 'manual') active @endif @if ($hasManualCityErrors) text-danger fw-semibold @endif"
                                                            type="button"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#client-org-city-tab-manual"
                                                            data-city-mode="manual"
                                                            role="tab"
                                                        >
                                                            {{ __('account.clients.tab_city_manual') }}
                                                        </button>
                                                    </li>
                                                </ul>

                                                <div class="tab-content">
                                                    <div
                                                        class="tab-pane fade @if ($cityLocationMode === 'catalog') show active @endif"
                                                        id="client-org-city-tab-catalog"
                                                        role="tabpanel"
                                                    >
                                                        <div class="row g-3">
                                                            <div class="col-12 col-md-6 position-relative">
                                                                <label class="form-label" for="client-org-city-search">{{ __('account.clients.city_search_label') }}</label>
                                                                <input
                                                                    type="text"
                                                                    id="client-org-city-search"
                                                                    class="form-control @error('city_id') is-invalid @enderror"
                                                                    placeholder="{{ __('account.clients.city_search_placeholder') }}"
                                                                    autocomplete="off"
                                                                    value="{{ old('city_search', $currentCity?->name ?? '') }}"
                                                                    data-city-field="catalog"
                                                                >
                                                                <small id="client-org-city-hint" class="form-text text-muted">{{ __('account.clients.city_search_hint') }}</small>
                                                                <input type="hidden" id="client-org-city-id" name="city_id" value="{{ old('city_id', $currentCity?->id) }}" data-city-field="catalog">
                                                                <div id="client-org-city-results" class="list-group position-absolute w-100 mt-1 shadow-sm z-3 overflow-auto" style="max-height: 22rem;"></div>
                                                                @error('city_id')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="col-12 col-md-6">
                                                                <label class="form-label" for="client-org-state">{{ __('account.clients.fields.state') }}</label>
                                                                <input type="text" id="client-org-state" class="form-control" value="{{ $currentCity?->state?->name ?? '' }}" readonly>
                                                            </div>

                                                            <div class="col-12 col-md-6">
                                                                <label class="form-label" for="client-org-country">{{ __('account.clients.fields.country') }}</label>
                                                                <input type="text" id="client-org-country" class="form-control" value="{{ $currentCity?->state?->country?->name ?? '' }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="tab-pane fade @if ($cityLocationMode === 'manual') show active @endif"
                                                        id="client-org-city-tab-manual"
                                                        role="tabpanel"
                                                    >
                                                        <p class="text-muted small mb-3">{{ __('account.clients.city_manual_hint') }}</p>
                                                        <div class="row g-3">
                                                            <div class="col-12 col-md-6">
                                                                <label class="form-label" for="client-org-city-manual">{{ __('account.clients.fields.city') }}</label>
                                                                <input
                                                                    type="text"
                                                                    id="client-org-city-manual"
                                                                    name="city"
                                                                    class="form-control @error('city') is-invalid @enderror"
                                                                    value="{{ old('city', $billingAddress?->city) }}"
                                                                    maxlength="255"
                                                                    data-city-field="manual"
                                                                >
                                                                @error('city')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="col-12 col-md-6">
                                                                <label class="form-label" for="client-org-state-manual">{{ __('account.clients.fields.state') }}</label>
                                                                <input
                                                                    type="text"
                                                                    id="client-org-state-manual"
                                                                    name="state"
                                                                    class="form-control @error('state') is-invalid @enderror"
                                                                    value="{{ old('state', $billingAddress?->state) }}"
                                                                    maxlength="255"
                                                                    data-city-field="manual"
                                                                >
                                                                @error('state')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="col-12 col-md-6">
                                                                <label class="form-label" for="client-org-country-manual">{{ __('account.clients.fields.country') }}</label>
                                                                <select
                                                                    id="client-org-country-manual"
                                                                    name="country_id"
                                                                    class="form-select @error('country_id') is-invalid @enderror"
                                                                    data-city-field="manual"
                                                                >
                                                                    <option value="">{{ __('account.clients.fields.country_placeholder') }}</option>
                                                                    @foreach ($countries as $country)
                                                                        <option value="{{ $country->id }}" @selected((int) old('country_id', $billingAddress?->country_id) === (int) $country->id)>
                                                                            {{ $country->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('country_id')
                                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-org-postal-code">{{ __('account.clients.fields.postal_code') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('postal_code') is-invalid @enderror"
                                                    id="client-org-postal-code"
                                                    name="postal_code"
                                                    value="{{ old('postal_code', $billingAddress?->postal_code) }}"
                                                    required
                                                    maxlength="255"
                                                >
                                                @error('postal_code')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade @if ($activeTab === 'tax_ids') show active @endif" id="client-org-tab-tax" role="tabpanel">
                                        <div id="client-org-tax-rows" class="d-flex flex-column gap-3">
                                            @forelse ($taxRows as $idx => $row)
                                                @include('account.clients.partials.tax-id-row', [
                                                    'idx' => $idx,
                                                    'row' => $row,
                                                    'taxIdCategories' => $taxIdCategories,
                                                ])
                                            @empty
                                                <p class="text-muted mb-0" id="client-org-tax-empty">{{ __('account.clients.tax_ids_empty') }}</p>
                                            @endforelse
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary mt-3" id="client-org-add-tax-row">
                                            {{ __('account.clients.add_tax_id_button') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.clients.cancel_button') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('account.clients.update_button') : __('account.clients.save_button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <template id="client-org-tax-row-template">
        @include('account.clients.partials.tax-id-row', [
            'idx' => '__INDEX__',
            'row' => ['id' => null, 'document_id' => null, 'value' => '', 'delete' => false],
            'taxIdCategories' => $taxIdCategories,
        ])
    </template>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    <script>
        (function () {
            const searchInput = document.getElementById('client-org-city-search');
            const cityIdInput = document.getElementById('client-org-city-id');
            const resultsBox = document.getElementById('client-org-city-results');
            const citySearchHint = document.getElementById('client-org-city-hint');
            const stateInput = document.getElementById('client-org-state');
            const countryInput = document.getElementById('client-org-country');
            const cityModeInput = document.getElementById('client-org-city-mode');
            const cityModeTabs = document.querySelectorAll('[data-city-mode]');
            const form = searchInput?.closest('form');
            const taxRowsContainer = document.getElementById('client-org-tax-rows');
            const addTaxRowBtn = document.getElementById('client-org-add-tax-row');
            const taxRowTemplate = document.getElementById('client-org-tax-row-template');
            const taxEmptyMsg = document.getElementById('client-org-tax-empty');
            let nextTaxIndex = {{ (int) $nextTaxIndex }};
            const MIN_CITY_QUERY_LEN = 4;
            const cityDetailsUrl = (id) => `{{ url('/account/company/cities') }}/${encodeURIComponent(id)}`;
            const citySearchUrl = `{{ route('account.cities.search') }}`;

            function catalogFields() {
                return Array.from(document.querySelectorAll('[data-city-field="catalog"]'));
            }

            function manualFields() {
                return Array.from(document.querySelectorAll('[data-city-field="manual"]'));
            }

            function setCityLocationMode(mode) {
                if (!cityModeInput) {
                    return;
                }
                cityModeInput.value = mode;
                const isCatalog = mode === 'catalog';
                catalogFields().forEach((field) => {
                    field.disabled = !isCatalog;
                });
                manualFields().forEach((field) => {
                    field.disabled = isCatalog;
                });
            }

            cityModeTabs.forEach((tab) => {
                tab.addEventListener('shown.bs.tab', (event) => {
                    const mode = event.target.getAttribute('data-city-mode');
                    if (mode === 'catalog' || mode === 'manual') {
                        setCityLocationMode(mode);
                    }
                });
            });

            if (cityModeInput) {
                setCityLocationMode(cityModeInput.value === 'manual' ? 'manual' : 'catalog');
            }

            form?.addEventListener('submit', () => {
                setCityLocationMode(cityModeInput?.value === 'manual' ? 'manual' : 'catalog');
            });

            if (searchInput && cityIdInput && resultsBox && stateInput && countryInput) {
                let currentAbortController = null;
                let debounceTimer = null;

                function clearResults() {
                    resultsBox.innerHTML = '';
                }

                async function applyCityDetails(cityId) {
                    if (!cityId) {
                        stateInput.value = '';
                        countryInput.value = '';
                        return;
                    }
                    try {
                        const response = await fetch(cityDetailsUrl(cityId), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!response.ok) {
                            stateInput.value = '';
                            countryInput.value = '';
                            return;
                        }
                        const city = await response.json();
                        stateInput.value = city.state || '';
                        countryInput.value = city.country || '';
                    } catch (e) {
                        stateInput.value = '';
                        countryInput.value = '';
                    }
                }

                function selectCity(city) {
                    cityIdInput.value = city.id;
                    searchInput.value = city.label || city.name || '';
                    clearResults();
                    applyCityDetails(city.id);
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
                    const response = await fetch(`${citySearchUrl}?q=${encodeURIComponent(query)}`, {
                        signal: currentAbortController.signal,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
                    if (debounceTimer) {
                        clearTimeout(debounceTimer);
                    }
                    if (query.length < MIN_CITY_QUERY_LEN) {
                        clearResults();
                        stateInput.value = '';
                        countryInput.value = '';
                        citySearchHint?.classList.remove('d-none');
                        return;
                    }
                    citySearchHint?.classList.add('d-none');
                    debounceTimer = setTimeout(async () => {
                        try {
                            const { results: cities, truncated } = await searchCities(query);
                            renderResults(cities, {
                                message: truncated ? @json(__('account.clients.city_search_truncated')) : null,
                            });
                        } catch (error) {
                            if (error.name !== 'AbortError') {
                                clearResults();
                            }
                        }
                    }, 300);
                });

                document.addEventListener('click', (event) => {
                    if (!resultsBox.contains(event.target) && event.target !== searchInput) {
                        clearResults();
                    }
                });

                if (cityIdInput.value) {
                    applyCityDetails(cityIdInput.value);
                }
            }

            if (addTaxRowBtn && taxRowsContainer && taxRowTemplate) {
                addTaxRowBtn.addEventListener('click', () => {
                    taxEmptyMsg?.remove();
                    const html = taxRowTemplate.innerHTML.replaceAll('__INDEX__', String(nextTaxIndex));
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    taxRowsContainer.appendChild(wrapper.firstElementChild);
                    nextTaxIndex += 1;
                });
            }

            const errorAlert = document.getElementById('client-org-form-errors');
            if (errorAlert) {
                requestAnimationFrame(() => {
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    errorAlert.focus({ preventScroll: true });
                });
                const firstInvalid = document.querySelector('#client-org-form .tab-pane.active .is-invalid');
                if (firstInvalid instanceof HTMLElement) {
                    firstInvalid.focus({ preventScroll: true });
                }
            }
        })();
    </script>
@endsection
