<div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold">{{ __('wizard.step7_validation_heading') }}</div>
            <ul class="mb-0 mt-2 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($saveMessage)
        <div class="alert alert-success py-2 small" role="status">{{ $saveMessage }}</div>
    @endif

    @if ($types->isEmpty())
        <div class="alert alert-warning mb-0" role="alert">
            {{ __('wizard.step7_no_types') }}
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('wizard.step7_intro') }}</p>

        <ul class="nav nav-tabs flex-wrap gap-1 gap-md-0" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($activeTab === 'types') active @endif"
                    wire:click="setTab('types')"
                >{{ __('wizard.step7_tab_types') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($activeTab === 'basics') active @endif"
                    wire:click="setTab('basics')"
                >{{ __('wizard.step7_tab_basics') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($activeTab === 'cuisines') active @endif"
                    wire:click="setTab('cuisines')"
                >{{ __('wizard.step7_tab_cuisines') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($activeTab === 'venues') active @endif"
                    wire:click="setTab('venues')"
                >{{ __('wizard.step7_tab_venues') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($activeTab === 'menus') active @endif"
                    wire:click="setTab('menus')"
                >{{ __('wizard.step7_tab_menus') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link @if ($activeTab === 'experience') active @endif"
                    wire:click="setTab('experience')"
                >{{ __('wizard.step7_tab_experience') }}</button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 rounded-bottom p-3 p-md-4 bg-white">
            @if ($activeTab === 'types')
                <p class="text-muted small">{{ __('wizard.step7_types_help') }}</p>
                <div class="row g-2">
                    @foreach ($types as $type)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input @error('gastronomyTypeIds') is-invalid @enderror"
                                    type="checkbox"
                                    wire:model.live="gastronomyTypeIds"
                                    value="{{ (string) $type->id }}"
                                    id="gastro-type-{{ $type->id }}"
                                >
                                <label class="form-check-label" for="gastro-type-{{ $type->id }}">
                                    {{ $type->name !== '' ? $type->name : $type->code }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('gastronomyTypeIds')
                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                @enderror
                @error('gastronomyTypeIds.*')
                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                @enderror
            @elseif ($activeTab === 'basics')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="gastro-city-query">{{ __('wizard.step7_field_city') }}</label>
                        <input
                            id="gastro-city-query"
                            type="search"
                            class="form-control @error('locationCityId') is-invalid @enderror"
                            wire:model.live.debounce.300ms="locationCityQuery"
                            placeholder="{{ __('wizard.step7_city_placeholder') }}"
                            autocomplete="off"
                        >
                        <small class="text-muted">{{ __('wizard.step7_city_hint') }}</small>
                        @error('locationCityId')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($citySearchNotice)
                            <div class="small text-muted mt-1">{{ $citySearchNotice }}</div>
                        @endif

                        @if ($locationCityResults !== [])
                            <div class="list-group mt-2 overflow-auto border rounded" style="max-height: 16rem;">
                                @foreach ($locationCityResults as $row)
                                    <button
                                        type="button"
                                        class="list-group-item list-group-item-action list-group-item-action py-2 text-start"
                                        wire:click="selectLocationCity({{ (int) $row['id'] }})"
                                    >{{ $row['label'] }}</button>
                                @endforeach
                            </div>
                        @endif

                        @if ($locationCityId)
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-2 p-2 bg-body-secondary bg-opacity-25 rounded border">
                                <span class="small mb-0">
                                    <span class="text-muted">{{ __('wizard.step7_city_selected') }}:</span>
                                    <span class="fw-medium">{{ $locationCityDisplayLabel }}</span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearLocationCity">
                                    {{ __('wizard.step7_city_clear') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="gastro-address">{{ __('wizard.step7_field_address') }}</label>
                        <textarea
                            id="gastro-address"
                            class="form-control @error('address') is-invalid @enderror"
                            wire:model.live="address"
                            rows="2"
                            maxlength="500"
                            placeholder="{{ __('wizard.step7_address_placeholder') }}"
                        ></textarea>
                        <p class="form-text text-muted mb-0">{{ __('wizard.step7_address_hint') }}</p>
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
                        $nominatimOn = (bool) config('services.nominatim.enabled');
                        $geocodeFormReady = trim((string) ($address ?? '')) !== '' && ($locationCityId ?? null) !== null && (int) $locationCityId > 0;
                        $geocodeButtonDisabled = $nominatimOn && ! $geocodeFormReady;
                    @endphp

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="gastro-lat">{{ __('wizard.step7_field_latitude') }}</label>
                        <input
                            id="gastro-lat"
                            type="text"
                            inputmode="decimal"
                            class="form-control @error('latitude') is-invalid @enderror"
                            wire:model.live="latitude"
                            placeholder="{{ __('wizard.step7_latitude_placeholder') }}"
                        >
                        @error('latitude')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="gastro-lon">{{ __('wizard.step7_field_longitude') }}</label>
                        <input
                            id="gastro-lon"
                            type="text"
                            inputmode="decimal"
                            class="form-control @error('longitude') is-invalid @enderror"
                            wire:model.live="longitude"
                            placeholder="{{ __('wizard.step7_longitude_placeholder') }}"
                        >
                        @error('longitude')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <x-coordinate-map-links :latitude="$latitude" :longitude="$longitude" />
                    </div>
                    <div class="col-12">
                        <p class="text-muted small mb-2">{{ __('wizard.step7_geocode_on_click_only') }}</p>
                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm"
                            wire:click="suggestCoordinatesFromAddress"
                            wire:loading.attr="disabled"
                            wire:target="suggestCoordinatesFromAddress"
                            @if ($geocodeButtonDisabled) disabled title="{{ __('wizard.step7_geocode_button_disabled_hint') }}" @endif
                        >
                            <span wire:loading.remove wire:target="suggestCoordinatesFromAddress">{{ __('wizard.step7_geocode_button') }}</span>
                            <span wire:loading wire:target="suggestCoordinatesFromAddress" class="spinner-border spinner-border-sm" role="status"></span>
                        </button>
                        @if ($geocodeFeedback)
                            <div class="small mt-1 {{ $geocodeSuccess ? 'text-success' : 'text-muted' }}" role="status">
                                {{ $geocodeFeedback }}
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <span class="form-label d-block mb-2">{{ __('wizard.step7_field_service_modes') }}</span>
                        <div class="row g-2">
                            <div class="col-6 col-md-4 col-lg">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gastro-indoor" wire:model="is_indoor">
                                    <label class="form-check-label" for="gastro-indoor">{{ __('wizard.step7_field_is_indoor') }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gastro-outdoor" wire:model="is_outdoor">
                                    <label class="form-check-label" for="gastro-outdoor">{{ __('wizard.step7_field_is_outdoor') }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gastro-takeaway" wire:model="has_takeaway">
                                    <label class="form-check-label" for="gastro-takeaway">{{ __('wizard.step7_field_has_takeaway') }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gastro-delivery" wire:model="has_delivery">
                                    <label class="form-check-label" for="gastro-delivery">{{ __('wizard.step7_field_has_delivery') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($activeTab === 'cuisines')
                @if ($cuisines->isEmpty())
                    <div class="alert alert-light border mb-0">{{ __('wizard.step7_empty_cuisines') }}</div>
                @else
                    <p class="text-muted small">{{ __('wizard.step7_cuisines_help') }}</p>
                    <div class="row g-2">
                        @foreach ($cuisines as $cuisine)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        wire:model.live="cuisineIds"
                                        value="{{ (string) $cuisine->id }}"
                                        id="cuisine-{{ $cuisine->id }}"
                                    >
                                    <label class="form-check-label" for="cuisine-{{ $cuisine->id }}">
                                        {{ $cuisine->name !== '' ? $cuisine->name : $cuisine->code }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif ($activeTab === 'venues')
                @if ($venues->isEmpty())
                    <div class="alert alert-light border mb-0">{{ __('wizard.step7_empty_venues') }}</div>
                @else
                    <p class="text-muted small">{{ __('wizard.step7_venues_help') }}</p>
                    <div class="row g-2">
                        @foreach ($venues as $venue)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        wire:model.live="venueIds"
                                        value="{{ (string) $venue->id }}"
                                        id="venue-{{ $venue->id }}"
                                    >
                                    <label class="form-check-label" for="venue-{{ $venue->id }}">
                                        {{ $venue->name !== '' ? $venue->name : $venue->code }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif ($activeTab === 'menus')
                @if ($menus->isEmpty())
                    <div class="alert alert-light border mb-0">{{ __('wizard.step7_empty_menus') }}</div>
                @else
                    <p class="text-muted small">{{ __('wizard.step7_menus_help') }}</p>
                    <div class="row g-2">
                        @foreach ($menus as $menu)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        wire:model.live="menuIds"
                                        value="{{ (string) $menu->id }}"
                                        id="menu-{{ $menu->id }}"
                                    >
                                    <label class="form-check-label" for="menu-{{ $menu->id }}">
                                        {{ $menu->name !== '' ? $menu->name : $menu->code }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif ($activeTab === 'experience')
                <p class="text-muted small">{{ __('wizard.step7_experience_help') }}</p>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="gastro-exp-duration">{{ __('wizard.step7_field_duration_minutes') }}</label>
                        <input
                            id="gastro-exp-duration"
                            type="number"
                            class="form-control @error('experienceDurationMinutes') is-invalid @enderror"
                            wire:model="experienceDurationMinutes"
                            min="0"
                            max="10080"
                            step="1"
                        >
                        @error('experienceDurationMinutes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gastro-exp-food" wire:model="experienceIncludesFood">
                            <label class="form-check-label" for="gastro-exp-food">{{ __('wizard.step7_field_includes_food') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gastro-exp-drinks" wire:model="experienceIncludesDrinks">
                            <label class="form-check-label" for="gastro-exp-drinks">{{ __('wizard.step7_field_includes_drinks') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gastro-exp-guided" wire:model="experienceIsGuided">
                            <label class="form-check-label" for="gastro-exp-guided">{{ __('wizard.step7_field_is_guided') }}</label>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button
                type="button"
                class="btn btn-primary"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">{{ __('wizard.step7_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status"></span>
            </button>
        </div>
    @endif
</div>
