@extends('layouts.base', ['title' => 'Prompt - Empresa'])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Empresa</h3>
                        <p class="mt-1 fw-medium">Edita los datos de tu empresa</p>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @php
                                $errorKeys = $errors->keys();
                                $hasGeneralErrors = collect($errorKeys)->contains(function (string $key): bool {
                                    return ! \Illuminate\Support\Str::startsWith($key, 'tax_ids.');
                                });
                                $hasTaxErrors = collect($errorKeys)->contains(function (string $key): bool {
                                    return \Illuminate\Support\Str::startsWith($key, 'tax_ids.');
                                });
                                $activeCompanyTab = $hasTaxErrors && ! $hasGeneralErrors ? 'tax_ids' : 'general';
                            @endphp

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <div class="fw-semibold">Revisá los datos antes de guardar.</div>
                                    <div>Hay errores de validación en una o más pestañas.</div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('account.company.update') }}" novalidate>
                                @csrf
                                @method('PUT')

                                <ul class="nav nav-tabs mb-3" id="company-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link @if ($activeCompanyTab === 'general') active @endif @if ($hasGeneralErrors) text-danger @endif" id="tab-general" data-bs-toggle="tab" data-bs-target="#tab-pane-general" type="button" role="tab" aria-controls="tab-pane-general" aria-selected="{{ $activeCompanyTab === 'general' ? 'true' : 'false' }}">
                                            General
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link @if ($activeCompanyTab === 'tax_ids') active @endif @if ($hasTaxErrors) text-danger @endif" id="tab-tax-ids" data-bs-toggle="tab" data-bs-target="#tab-pane-tax-ids" type="button" role="tab" aria-controls="tab-pane-tax-ids" aria-selected="{{ $activeCompanyTab === 'tax_ids' ? 'true' : 'false' }}">
                                            Datos fiscales
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade @if ($activeCompanyTab === 'general') show active @endif" id="tab-pane-general" role="tabpanel" aria-labelledby="tab-general" tabindex="0">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tipo de empresa</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="{{ $accountTypeLabel ?? '—' }}"
                                                        readonly
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_name" class="form-label">Nombre</label>
                                                    <input
                                                        type="text"
                                                        id="company_name"
                                                        name="name"
                                                        required
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name', $account->name) }}"
                                                    >
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_commercial_name" class="form-label">Nombre comercial</label>
                                                    <input
                                                        type="text"
                                                        id="company_commercial_name"
                                                        name="commercial_name"
                                                        required
                                                        class="form-control @error('commercial_name') is-invalid @enderror"
                                                        value="{{ old('commercial_name', $account->commercial_name) }}"
                                                    >
                                                    @error('commercial_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_email" class="form-label">Email</label>
                                                    <input
                                                        type="email"
                                                        id="company_email"
                                                        name="email"
                                                        required
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        value="{{ old('email', $account->email) }}"
                                                    >
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_phone" class="form-label">Teléfono</label>
                                                    <input
                                                        type="text"
                                                        id="company_phone"
                                                        name="phone"
                                                        required
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        value="{{ old('phone', $account->phone) }}"
                                                    >
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_address_line1" class="form-label">Dirección (línea 1)</label>
                                                    <input
                                                        type="text"
                                                        id="company_address_line1"
                                                        name="address_line1"
                                                        required
                                                        class="form-control @error('address_line1') is-invalid @enderror"
                                                        value="{{ old('address_line1', $account->address_line1) }}"
                                                    >
                                                    @error('address_line1')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_address_line2" class="form-label">Dirección (línea 2) <span class="text-muted fw-normal">(opcional)</span></label>
                                                    <input
                                                        type="text"
                                                        id="company_address_line2"
                                                        name="address_line2"
                                                        class="form-control @error('address_line2') is-invalid @enderror"
                                                        value="{{ old('address_line2', $account->address_line2) }}"
                                                    >
                                                    @error('address_line2')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3 position-relative">
                                                    <label for="city_search" class="form-label">Ciudad</label>
                                                    <input
                                                        type="text"
                                                        id="city_search"
                                                        class="form-control @error('city_id') is-invalid @enderror"
                                                        placeholder="Escribe al menos 4 letras para buscar…"
                                                        autocomplete="off"
                                                        value="{{ old('city_search', $currentCity?->name ?? '') }}"
                                                    >
                                                    <small id="city-search-hint" class="form-text text-muted">La búsqueda empieza con 4 caracteres.</small>
                                                    <input type="hidden" id="city_id" name="city_id" value="{{ old('city_id', $account->city_id) }}" required>
                                                    <div id="city-results" class="list-group position-absolute w-100 mt-1 shadow-sm z-3 overflow-auto" style="max-height: 22rem;"></div>
                                                    @error('city_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <input
                                                        id="state_name"
                                                        type="text"
                                                        class="form-control"
                                                        value="{{ $currentCity?->state?->name ?? '' }}"
                                                        readonly
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">País</label>
                                                    <input
                                                        id="country_name"
                                                        type="text"
                                                        class="form-control"
                                                        value="{{ $currentCity?->state?->country?->name ?? '' }}"
                                                        readonly
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="company_postal_code" class="form-label">Código postal</label>
                                                    <input
                                                        type="text"
                                                        id="company_postal_code"
                                                        name="postal_code"
                                                        required
                                                        class="form-control @error('postal_code') is-invalid @enderror"
                                                        value="{{ old('postal_code', $account->postal_code) }}"
                                                    >
                                                    @error('postal_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade @if ($activeCompanyTab === 'tax_ids') show active @endif" id="tab-pane-tax-ids" role="tabpanel" aria-labelledby="tab-tax-ids" tabindex="0">
                                        @php
                                            $oldTaxIds = old('tax_ids');
                                            $taxRows = [];
                                            if (is_array($oldTaxIds)) {
                                                $taxRows = array_values($oldTaxIds);
                                            } else {
                                                foreach ($taxIds as $taxId) {
                                                    $taxRows[] = [
                                                        'id' => $taxId->id,
                                                        'account_category_id' => $taxId->account_category_id,
                                                        'value' => $taxId->value,
                                                        'delete' => false,
                                                    ];
                                                }
                                            }
                                            $nextTaxIndex = count($taxRows);
                                        @endphp

                                        <div id="tax-id-rows">
                                            @forelse($taxRows as $idx => $row)
                                                @php $existingTaxId = isset($row['id']) ? (int) $row['id'] : null; @endphp
                                                <div class="row g-2 align-items-end border rounded p-2 mb-2 tax-id-row">
                                                    @if($existingTaxId)
                                                        <input type="hidden" name="tax_ids[{{ $idx }}][id]" value="{{ $existingTaxId }}">
                                                    @endif
                                                    <div class="col-md-4">
                                                        <label class="form-label">Tipo fiscal</label>
                                                        <select name="tax_ids[{{ $idx }}][account_category_id]" class="form-select @error("tax_ids.$idx.account_category_id") is-invalid @enderror">
                                                            <option value="">Seleccioná un tipo</option>
                                                            @foreach($taxIdCategories as $category)
                                                                <option value="{{ $category->id }}" @selected((int) ($row['account_category_id'] ?? 0) === (int) $category->id)>
                                                                    {{ $category->name ?: $category->code }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("tax_ids.$idx.account_category_id")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Valor</label>
                                                        <input type="text"
                                                               name="tax_ids[{{ $idx }}][value]"
                                                               class="form-control @error("tax_ids.$idx.value") is-invalid @enderror"
                                                               value="{{ (string) ($row['value'] ?? '') }}"
                                                               placeholder="Ingresá el dato fiscal">
                                                        @error("tax_ids.$idx.value")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if($existingTaxId)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox"
                                                                       id="delete-tax-id-{{ $existingTaxId }}"
                                                                       name="tax_ids[{{ $idx }}][delete]" value="1"
                                                                       @checked((bool) ($row['delete'] ?? false))>
                                                                <label class="form-check-label" for="delete-tax-id-{{ $existingTaxId }}">
                                                                    Eliminar
                                                                </label>
                                                            </div>
                                                        @else
                                                            <button type="button" class="btn btn-outline-danger btn-sm js-remove-tax-row">Quitar</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">Todavía no hay datos fiscales cargados.</p>
                                            @endforelse
                                        </div>

                                        <button type="button" class="btn btn-outline-secondary" id="btn-add-tax-row">Agregar dato fiscal</button>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a class="btn btn-light" href="{{ route('account.dashboard') }}">Volver</a>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const searchInput = document.getElementById('city_search');
            const cityIdInput = document.getElementById('city_id');
            const resultsBox = document.getElementById('city-results');
            const citySearchHint = document.getElementById('city-search-hint');
            const stateInput = document.getElementById('state_name');
            const countryInput = document.getElementById('country_name');
            const taxRowsContainer = document.getElementById('tax-id-rows');
            const addTaxRowBtn = document.getElementById('btn-add-tax-row');
            const MIN_CITY_QUERY_LEN = 4;
            const cityDetailsUrl = (id) => `{{ url('/account/company/cities') }}/${encodeURIComponent(id)}`;

            if (!searchInput || !cityIdInput || !resultsBox || !stateInput || !countryInput) {
                return;
            }

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
                const displayName = city.name || '';
                searchInput.value = displayName;
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

                if (!cities.length) {
                    return;
                }

                cities.forEach((city) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.textContent = city.name;
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

            if (cityIdInput.value) {
                applyCityDetails(cityIdInput.value);
            }

            if (taxRowsContainer && addTaxRowBtn) {
                let taxNextIndex = {{ $nextTaxIndex ?? 0 }};
                const taxCategoryOptions = @json(collect($taxIdCategories)->map(fn ($category) => ['id' => (int) $category->id, 'label' => (string) ($category->name ?: $category->code)])->values());
                function buildTaxCategoryOptions() {
                    let html = '<option value="">Seleccioná un tipo</option>';
                    taxCategoryOptions.forEach(function (opt) {
                        html += '<option value="' + opt.id + '">' + opt.label + '</option>';
                    });
                    return html;
                }

                addTaxRowBtn.addEventListener('click', function () {
                    const idx = taxNextIndex++;
                    const row = document.createElement('div');
                    row.className = 'row g-2 align-items-end border rounded p-2 mb-2 tax-id-row';
                    row.innerHTML =
                        '<div class="col-md-4">' +
                            '<label class="form-label">Tipo fiscal</label>' +
                            '<select name="tax_ids[' + idx + '][account_category_id]" class="form-select">' +
                                buildTaxCategoryOptions() +
                            '</select>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<label class="form-label">Valor</label>' +
                            '<input type="text" name="tax_ids[' + idx + '][value]" class="form-control" placeholder="Ingresá el dato fiscal">' +
                        '</div>' +
                        '<div class="col-md-2">' +
                            '<button type="button" class="btn btn-outline-danger btn-sm js-remove-tax-row">Quitar</button>' +
                        '</div>';
                    taxRowsContainer.appendChild(row);
                });

                taxRowsContainer.addEventListener('click', function (event) {
                    const target = event.target;
                    if (target && target.classList.contains('js-remove-tax-row')) {
                        const row = target.closest('.tax-id-row');
                        if (row) {
                            row.remove();
                        }
                    }
                });
            }
        })();
    </script>

    <x-site-footer-simple />

@endsection

