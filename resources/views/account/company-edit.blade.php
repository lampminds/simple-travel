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
                            <form method="POST" action="{{ route('account.company.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input
                                                type="text"
                                                name="name"
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
                                            <label class="form-label">Nombre comercial</label>
                                            <input
                                                type="text"
                                                name="commercial_name"
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
                                            <label class="form-label">Email</label>
                                            <input
                                                type="email"
                                                name="email"
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
                                            <label class="form-label">Teléfono</label>
                                            <input
                                                type="text"
                                                name="phone"
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
                                            <label class="form-label">Dirección (línea 1)</label>
                                            <input
                                                type="text"
                                                name="address_line1"
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
                                            <label class="form-label">Dirección (línea 2)</label>
                                            <input
                                                type="text"
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
                                            <input type="hidden" id="city_id" name="city_id" value="{{ old('city_id', $account->city_id) }}">
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
                                            <label class="form-label">Código postal</label>
                                            <input
                                                type="text"
                                                name="postal_code"
                                                class="form-control @error('postal_code') is-invalid @enderror"
                                                value="{{ old('postal_code', $account->postal_code) }}"
                                            >
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
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
        })();
    </script>

    <x-site-footer-simple />

@endsection

