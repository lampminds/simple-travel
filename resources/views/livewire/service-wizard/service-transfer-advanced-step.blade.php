<div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold">{{ __('wizard.step7_transfer_validation_heading') }}</div>
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

    <ul class="nav nav-tabs flex-wrap gap-1 gap-md-0 mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button type="button" class="nav-link @if ($activeTab === 'basics') active @endif" wire:click="setTab('basics')">
                {{ __('wizard.step7_transfer_tab_basics') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button type="button" class="nav-link @if ($activeTab === 'routes') active @endif" wire:click="setTab('routes')">
                {{ __('wizard.step7_transfer_tab_routes') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button type="button" class="nav-link @if ($activeTab === 'prices') active @endif" wire:click="setTab('prices')">
                {{ __('wizard.step7_transfer_tab_prices') }}
            </button>
        </li>
    </ul>

    @if ($activeTab === 'basics')
        <p class="text-muted small mb-3">{{ __('wizard.step7_transfer_intro_basics') }}</p>
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="xfer-type">{{ __('wizard.step7_transfer_field_transfer_type') }}</label>
                <select id="xfer-type" class="form-select" wire:model="transfer_type">
                    <option value="one_way">{{ __('wizard.step7_transfer_type_one_way') }}</option>
                    <option value="round_trip">{{ __('wizard.step7_transfer_type_round_trip') }}</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="xfer-modality">{{ __('wizard.step7_transfer_field_modality') }}</label>
                <select id="xfer-modality" class="form-select" wire:model="modality">
                    <option value="private">{{ __('wizard.step7_transfer_modality_private') }}</option>
                    <option value="shared">{{ __('wizard.step7_transfer_modality_shared') }}</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="xfer-def-dur">{{ __('wizard.step7_transfer_field_default_duration') }}</label>
                <input id="xfer-def-dur" type="number" class="form-control" wire:model="default_duration_minutes" min="0" step="1">
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="xfer-max-pax">{{ __('wizard.step7_transfer_field_max_passengers') }}</label>
                <input id="xfer-max-pax" type="number" class="form-control" wire:model="max_passengers" min="0" step="1">
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="xfer-max-lug">{{ __('wizard.step7_transfer_field_max_luggage') }}</label>
                <input id="xfer-max-lug" type="number" class="form-control" wire:model="max_luggage" min="0" step="1">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="xfer-multi" wire:model="allows_multiple_stops">
                    <label class="form-check-label" for="xfer-multi">{{ __('wizard.step7_transfer_field_allows_multi_stops') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="xfer-flight" wire:model="requires_flight_info">
                    <label class="form-check-label" for="xfer-flight">{{ __('wizard.step7_transfer_field_requires_flight') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="xfer-pu" wire:model="requires_pickup_time">
                    <label class="form-check-label" for="xfer-pu">{{ __('wizard.step7_transfer_field_requires_pickup') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="xfer-do" wire:model="requires_dropoff_time">
                    <label class="form-check-label" for="xfer-do">{{ __('wizard.step7_transfer_field_requires_dropoff') }}</label>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-primary" wire:click="saveBasics" wire:loading.attr="disabled" wire:target="saveBasics">
                <span wire:loading.remove wire:target="saveBasics">{{ __('wizard.step7_transfer_save_basics') }}</span>
                <span wire:loading wire:target="saveBasics" class="spinner-border spinner-border-sm" role="status"></span>
            </button>
        </div>
    @endif

    @if ($activeTab === 'routes')
        <p class="text-muted small mb-3">{{ __('wizard.step7_transfer_intro_routes') }}</p>
        @if ($locations->isEmpty())
            @if ($transferLocationBootstrapTemplateEmpty)
                <div class="alert alert-warning mb-0" role="alert">
                    @if ($serviceCityId !== null)
                        {{ __('wizard.step7_transfer_no_locations_in_service_city') }}
                    @else
                        {{ __('wizard.step7_transfer_no_locations') }}
                    @endif
                </div>
            @else
                <div class="alert alert-light border mb-0" role="status">
                    <p class="small mb-0">{{ __('wizard.transfer_location_bootstrap_routes_hint') }}</p>
                    @if (! $showTransferLocationBootstrapModal)
                        <button type="button" class="btn btn-primary btn-sm mt-2" wire:click="reopenTransferLocationBootstrapModal">
                            {{ __('wizard.transfer_location_bootstrap_open_button') }}
                        </button>
                    @endif
                </div>
            @endif
        @else
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <p class="text-muted small mb-0">{{ __('wizard.step7_transfer_routes_list_hint') }}</p>
                <button type="button" class="btn btn-primary btn-sm" wire:click="openAddRouteModal">
                    {{ __('wizard.step7_transfer_add_route') }}
                </button>
            </div>

            @if ($transfer->routes->isEmpty())
                <div class="alert alert-light border mb-0">{{ __('wizard.step7_transfer_routes_empty') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('wizard.step7_transfer_route_origin') }}</th>
                                <th>{{ __('wizard.step7_transfer_route_destination') }}</th>
                                <th class="text-center">{{ __('wizard.step7_transfer_col_route_active') }}</th>
                                <th class="text-end">{{ __('wizard.step7_transfer_col_distance') }}</th>
                                <th class="text-end">{{ __('wizard.step7_transfer_col_duration') }}</th>
                                <th class="text-end">{{ __('wizard.step7_transfer_col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer->routes as $r)
                                <tr wire:key="route-{{ $r->id }}">
                                    <td>{{ $r->origin?->wizardRouteSelectLabel() ?? '—' }}</td>
                                    <td>{{ $r->destination?->wizardRouteSelectLabel() ?? '—' }}</td>
                                    <td class="text-center">
                                        @if ($r->is_active)
                                            <span class="badge text-bg-success">{{ __('wizard.step7_transfer_route_status_active') }}</span>
                                        @else
                                            <span class="badge text-bg-secondary">{{ __('wizard.step7_transfer_route_status_inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $r->distance_km !== null ? $r->distance_km : '—' }}</td>
                                    <td class="text-end">{{ $r->duration_minutes !== null ? $r->duration_minutes : '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openEditRouteModal({{ (int) $r->id }})">
                                                {{ __('wizard.step7_transfer_edit_route') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRoute({{ (int) $r->id }})">
                                                {{ __('wizard.step7_transfer_remove') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    @endif

    @if ($activeTab === 'prices')
        <p class="text-muted small mb-3">{{ __('wizard.step7_transfer_intro_prices') }}</p>
        @if ($currencies->isEmpty())
            <div class="alert alert-warning mb-0" role="alert">{{ __('wizard.step7_transfer_no_currencies') }}</div>
        @else
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <p class="text-muted small mb-0">{{ __('wizard.step7_transfer_prices_list_hint') }}</p>
                <button type="button" class="btn btn-primary btn-sm" wire:click="openAddPriceModal">
                    {{ __('wizard.step7_transfer_add_price') }}
                </button>
            </div>

            @if ($transfer->prices->isEmpty())
                <div class="alert alert-light border mb-0">{{ __('wizard.step7_transfer_prices_empty') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('wizard.step7_transfer_col_route') }}</th>
                                <th>{{ __('wizard.step7_transfer_col_vehicle') }}</th>
                                <th>{{ __('wizard.step7_transfer_col_pricing') }}</th>
                                <th>{{ __('wizard.step7_transfer_col_amounts') }}</th>
                                <th class="text-end">{{ __('wizard.step7_transfer_col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer->prices as $p)
                                <tr wire:key="price-{{ $p->id }}">
                                    <td>
                                        @if ($p->route_id)
                                            {{ $p->route?->origin?->wizardRouteSelectLabel() ?? '?' }} → {{ $p->route?->destination?->wizardRouteSelectLabel() ?? '?' }}
                                        @else
                                            {{ __('wizard.step7_transfer_price_all_routes') }}
                                        @endif
                                    </td>
                                    <td>{{ $p->vehicleType?->wizardPricingVehicleLabel() ?? __('wizard.step7_transfer_price_any_vehicle') }}</td>
                                    <td>
                                        {{ $p->pricing_type === 'per_vehicle' ? __('wizard.step7_transfer_pricing_per_vehicle') : __('wizard.step7_transfer_pricing_per_person') }}
                                        <span class="text-muted small">({{ $p->currency?->display_name ?? '#' }})</span>
                                    </td>
                                    <td class="small">
                                        @if ($p->base_price !== null)
                                            {{ __('wizard.step7_transfer_col_base') }}: {{ $p->base_price }}
                                        @endif
                                        @if ($p->price_per_person !== null)
                                            @if ($p->base_price !== null)<br>@endif
                                            {{ __('wizard.step7_transfer_col_per_person') }}: {{ $p->price_per_person }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openEditPriceModal({{ (int) $p->id }})">
                                                {{ __('wizard.step7_transfer_edit_price') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removePrice({{ (int) $p->id }})">
                                                {{ __('wizard.step7_transfer_remove') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    @endif

    @if ($showAddRouteModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background-color: rgba(0, 0, 0, 0.45);"
            wire:keydown.escape.window="closeAddRouteModal"
        >
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content" wire:click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingRouteId ? __('wizard.step7_transfer_route_modal_title_edit') : __('wizard.step7_transfer_route_modal_title') }}</h5>
                        <button type="button" class="btn-close" aria-label="{{ __('wizard.step7_transfer_modal_cancel') }}" wire:click="closeAddRouteModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="xfer-route-modal-o">{{ __('wizard.step7_transfer_route_origin') }}</label>
                                <select id="xfer-route-modal-o" class="form-select @error('origin') is-invalid @enderror" wire:model.number="newRouteOriginId">
                                    <option value="0">{{ __('wizard.step7_transfer_select_location') }}</option>
                                    @foreach ($locationRouteGroups as $group)
                                        @php
                                            $routeGroupCategory = $group['category'];
                                            $routeGroupLabel = $routeGroupCategory !== null
                                                ? ($routeGroupCategory->name !== '' ? $routeGroupCategory->name : $routeGroupCategory->code)
                                                : __('wizard.step7_transfer_location_category_other');
                                        @endphp
                                        <optgroup label="{{ $routeGroupLabel }}">
                                            @foreach ($group['locations'] as $loc)
                                                <option value="{{ (int) $loc->id }}">{{ $loc->wizardRouteSelectLabel() }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('origin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="xfer-route-modal-d">{{ __('wizard.step7_transfer_route_destination') }}</label>
                                <select id="xfer-route-modal-d" class="form-select @error('destination') is-invalid @enderror" wire:model.number="newRouteDestinationId">
                                    <option value="0">{{ __('wizard.step7_transfer_select_location') }}</option>
                                    @foreach ($locationRouteGroups as $group)
                                        @php
                                            $routeGroupCategory = $group['category'];
                                            $routeGroupLabel = $routeGroupCategory !== null
                                                ? ($routeGroupCategory->name !== '' ? $routeGroupCategory->name : $routeGroupCategory->code)
                                                : __('wizard.step7_transfer_location_category_other');
                                        @endphp
                                        <optgroup label="{{ $routeGroupLabel }}">
                                            @foreach ($group['locations'] as $loc)
                                                <option value="{{ (int) $loc->id }}">{{ $loc->wizardRouteSelectLabel() }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('destination')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="xfer-route-active" wire:model="new_route_is_active">
                                    <label class="form-check-label" for="xfer-route-active">{{ __('wizard.step7_transfer_route_active') }}</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="xfer-route-dist">{{ __('wizard.step7_transfer_field_route_distance_km') }}</label>
                                <input id="xfer-route-dist" type="text" inputmode="decimal" class="form-control @error('new_route_distance_km') is-invalid @enderror" wire:model="new_route_distance_km" autocomplete="off">
                                @error('new_route_distance_km')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="xfer-route-dur">{{ __('wizard.step7_transfer_field_route_duration_min') }}</label>
                                <input id="xfer-route-dur" type="number" class="form-control @error('new_route_duration_minutes') is-invalid @enderror" wire:model="new_route_duration_minutes" min="0" step="1" autocomplete="off">
                                @error('new_route_duration_minutes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeAddRouteModal">{{ __('wizard.step7_transfer_modal_cancel') }}</button>
                        <button type="button" class="btn btn-primary" wire:click="saveRoute" wire:loading.attr="disabled" wire:target="saveRoute">
                            <span wire:loading.remove wire:target="saveRoute">{{ $editingRouteId ? __('wizard.step7_transfer_modal_update_route') : __('wizard.step7_transfer_modal_save_route') }}</span>
                            <span wire:loading wire:target="saveRoute" class="spinner-border spinner-border-sm" role="status"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showAddPriceModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background-color: rgba(0, 0, 0, 0.45);"
            wire:keydown.escape.window="closeAddPriceModal"
        >
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content" wire:click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingPriceId ? __('wizard.step7_transfer_price_modal_title_edit') : __('wizard.step7_transfer_price_modal_title') }}</h5>
                        <button type="button" class="btn-close" aria-label="{{ __('wizard.step7_transfer_modal_cancel') }}" wire:click="closeAddPriceModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label" for="p-route-m">{{ __('wizard.step7_transfer_price_route') }}</label>
                                <select id="p-route-m" class="form-select" wire:model="price_route_id">
                                    <option value="">{{ __('wizard.step7_transfer_price_all_routes') }}</option>
                                    @foreach ($transfer->routes as $r)
                                        <option value="{{ (int) $r->id }}">
                                            {{ $r->origin?->wizardRouteSelectLabel() ?? '?' }} → {{ $r->destination?->wizardRouteSelectLabel() ?? '?' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label" for="p-vt-m">{{ __('wizard.step7_transfer_price_vehicle_optional') }}</label>
                                <select id="p-vt-m" class="form-select" wire:model="price_vehicle_type_id">
                                    <option value="">{{ __('wizard.step7_transfer_price_any_vehicle') }}</option>
                                    @foreach ($vehicleTypes as $vt)
                                        <option value="{{ (int) $vt->id }}">{{ $vt->wizardPricingVehicleLabel() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label" for="p-ptype-m">{{ __('wizard.step7_transfer_field_pricing_type') }}</label>
                                <select id="p-ptype-m" class="form-select" wire:model="price_pricing_type">
                                    <option value="per_vehicle">{{ __('wizard.step7_transfer_pricing_per_vehicle') }}</option>
                                    <option value="per_person">{{ __('wizard.step7_transfer_pricing_per_person') }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label" for="p-cur-m">{{ __('wizard.step7_transfer_field_currency') }}</label>
                                <select id="p-cur-m" class="form-select @error('price_currency_id') is-invalid @enderror" wire:model.number="price_currency_id">
                                    @foreach ($currencies as $cur)
                                        <option value="{{ (int) $cur->id }}">{{ $cur->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('price_currency_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-base-m">{{ __('wizard.step7_transfer_field_base_price') }}</label>
                                <input id="p-base-m" type="text" inputmode="decimal" class="form-control @error('price_base_price') is-invalid @enderror" wire:model="price_base_price">
                                @error('price_base_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-ppp-m">{{ __('wizard.step7_transfer_field_price_per_person') }}</label>
                                <input id="p-ppp-m" type="text" inputmode="decimal" class="form-control @error('price_per_person') is-invalid @enderror" wire:model="price_per_person">
                                @error('price_per_person')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-ppe-m">{{ __('wizard.step7_transfer_field_price_extra') }}</label>
                                <input id="p-ppe-m" type="text" inputmode="decimal" class="form-control" wire:model="price_per_extra_passenger">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-minp-m">{{ __('wizard.step7_transfer_field_min_pax') }}</label>
                                <input id="p-minp-m" type="number" class="form-control" wire:model="price_min_passengers" min="0" step="1">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-maxp-m">{{ __('wizard.step7_transfer_field_max_pax') }}</label>
                                <input id="p-maxp-m" type="number" class="form-control" wire:model="price_max_passengers" min="0" step="1">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-price-valid-from-m">{{ __('wizard.step7_transfer_field_valid_from') }}</label>
                                <input id="p-price-valid-from-m" type="date" class="form-control @error('price_valid_from') is-invalid @enderror" wire:model="price_valid_from">
                                @error('price_valid_from')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="p-price-valid-to-m">{{ __('wizard.step7_transfer_field_valid_to') }}</label>
                                <input id="p-price-valid-to-m" type="date" class="form-control @error('price_valid_to') is-invalid @enderror" wire:model="price_valid_to">
                                @error('price_valid_to')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeAddPriceModal">{{ __('wizard.step7_transfer_modal_cancel') }}</button>
                        <button type="button" class="btn btn-primary" wire:click="savePrice" wire:loading.attr="disabled" wire:target="savePrice">
                            <span wire:loading.remove wire:target="savePrice">{{ $editingPriceId ? __('wizard.step7_transfer_modal_update_price') : __('wizard.step7_transfer_modal_save_price') }}</span>
                            <span wire:loading wire:target="savePrice" class="spinner-border spinner-border-sm" role="status"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showTransferVehicleBootstrapModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background-color: rgba(0, 0, 0, 0.45);"
            wire:keydown.escape.window="dismissTransferVehicleBootstrapModal"
        >
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content" wire:click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('wizard.transfer_bootstrap_modal_title') }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="{{ __('wizard.transfer_bootstrap_skip') }}"
                            wire:click="dismissTransferVehicleBootstrapModal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">{{ __('wizard.transfer_bootstrap_modal_intro') }}</p>

                        @if ($bootstrapModalGrouped->isEmpty())
                            <div class="alert alert-warning mb-0" role="alert">
                                {{ __('wizard.transfer_bootstrap_template_empty') }}
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <span class="text-muted small me-1">{{ __('wizard.category_bulk_hint') }}</span>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    wire:click="selectAllBootstrapCatalogCategories"
                                    wire:loading.attr="disabled"
                                    wire:target="selectAllBootstrapCatalogCategories,clearAllBootstrapCatalogCategories,applyTransferVehicleBootstrapImport"
                                >
                                    {{ __('wizard.category_select_all') }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    wire:click="clearAllBootstrapCatalogCategories"
                                    wire:loading.attr="disabled"
                                    wire:target="selectAllBootstrapCatalogCategories,clearAllBootstrapCatalogCategories,applyTransferVehicleBootstrapImport"
                                >
                                    {{ __('wizard.category_select_none') }}
                                </button>
                            </div>

                            <div class="row g-2 mb-4">
                                @foreach ($bootstrapModalCategoryOptions as $cid => $label)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                wire:model.live="bootstrapCatalogCategoryIds"
                                                value="{{ (string) $cid }}"
                                                id="xfer-boot-cat-{{ $cid }}"
                                            >
                                            <label class="form-check-label" for="xfer-boot-cat-{{ $cid }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (collect($bootstrapCatalogCategoryIds)->isEmpty())
                                <div class="alert alert-light border mb-0" role="alert">
                                    {{ __('wizard.transfer_bootstrap_no_categories_selected') }}
                                </div>
                            @else
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span class="text-muted small me-1">{{ __('wizard.features_bulk_hint') }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="selectAllVisibleBootstrapCatalogTypes"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllVisibleBootstrapCatalogTypes,clearAllBootstrapCatalogTypes,applyTransferVehicleBootstrapImport"
                                    >
                                        {{ __('wizard.features_select_all') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearAllBootstrapCatalogTypes"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllVisibleBootstrapCatalogTypes,clearAllBootstrapCatalogTypes,applyTransferVehicleBootstrapImport"
                                    >
                                        {{ __('wizard.features_select_none') }}
                                    </button>
                                </div>

                                <div class="row g-3">
                                    @foreach ($bootstrapModalGrouped as $categoryId => $typesInCat)
                                        @php
                                            $categoryId = (int) $categoryId;
                                            $categoryTitle = $bootstrapModalCategoryOptions[$categoryId] ?? __('wizard.transfer_bootstrap_category_other');
                                        @endphp
                                        @if (in_array((string) $categoryId, $bootstrapCatalogCategoryIds, true))
                                            <div class="col-12" wire:key="xfer-boot-grp-{{ $categoryId }}">
                                                <div class="card border">
                                                    <div class="card-header py-2 bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                        <h6 class="mb-0">{{ $categoryTitle }}</h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                wire:click="selectAllTypesInBootstrapCategory({{ $categoryId }})"
                                                                wire:loading.attr="disabled"
                                                                wire:target="selectAllTypesInBootstrapCategory,clearTypesInBootstrapCategory,applyTransferVehicleBootstrapImport"
                                                            >
                                                                {{ __('wizard.features_select_all') }}
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                wire:click="clearTypesInBootstrapCategory({{ $categoryId }})"
                                                                wire:loading.attr="disabled"
                                                                wire:target="selectAllTypesInBootstrapCategory,clearTypesInBootstrapCategory,applyTransferVehicleBootstrapImport"
                                                            >
                                                                {{ __('wizard.features_select_none') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body py-2">
                                                        <div class="row row-cols-1 row-cols-md-2 g-2">
                                                            @foreach ($typesInCat as $vt)
                                                                <div class="col" wire:key="xfer-boot-vt-{{ $vt->id }}">
                                                                    <div class="border rounded bg-white p-3 h-100">
                                                                        <div class="form-check m-0">
                                                                            <input
                                                                                class="form-check-input"
                                                                                type="checkbox"
                                                                                id="xfer-boot-vt-{{ $vt->id }}"
                                                                                value="{{ (string) $vt->id }}"
                                                                                wire:model="bootstrapCatalogTypeIds"
                                                                            >
                                                                            <label class="form-check-label" for="xfer-boot-vt-{{ $vt->id }}">
                                                                                <span class="fw-medium">{{ $vt->name }}</span>
                                                                                <span class="text-muted small">
                                                                                    — {{ __('wizard.step7_transfer_vehicle_max_pax', ['n' => (int) ($vt->max_passengers ?? 0)]) }}
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @error('bootstrapCatalogTypeIds')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" wire:click="dismissTransferVehicleBootstrapModal">
                            {{ __('wizard.transfer_bootstrap_skip') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            wire:click="applyTransferVehicleBootstrapImport"
                            wire:loading.attr="disabled"
                            wire:target="applyTransferVehicleBootstrapImport"
                            @if ($bootstrapModalGrouped->isEmpty()) disabled @endif
                        >
                            <span wire:loading.remove wire:target="applyTransferVehicleBootstrapImport">{{ __('wizard.transfer_bootstrap_import') }}</span>
                            <span wire:loading wire:target="applyTransferVehicleBootstrapImport" class="spinner-border spinner-border-sm" role="status"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showTransferLocationBootstrapModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background-color: rgba(0, 0, 0, 0.45);"
            wire:keydown.escape.window="dismissTransferLocationBootstrapModal"
        >
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content" wire:click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('wizard.transfer_location_bootstrap_modal_title') }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="{{ __('wizard.transfer_location_bootstrap_skip') }}"
                            wire:click="dismissTransferLocationBootstrapModal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">{{ __('wizard.transfer_location_bootstrap_modal_intro') }}</p>

                        @if ($locationBootstrapCityOptions === [])
                            <div class="alert alert-warning mb-0" role="alert">
                                {{ __('wizard.transfer_location_bootstrap_template_empty') }}
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label" for="xfer-loc-city">{{ __('wizard.transfer_location_bootstrap_field_city') }}</label>
                                <select id="xfer-loc-city" class="form-select" wire:model.live="bootstrapLocationCityId">
                                    <option value="0">{{ __('wizard.transfer_location_bootstrap_pick_city') }}</option>
                                    @foreach ($locationBootstrapCityOptions as $cid => $cityName)
                                        <option value="{{ (int) $cid }}">{{ $cityName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if ((int) $bootstrapLocationCityId < 1)
                                <div class="alert alert-light border mb-0" role="alert">
                                    {{ __('wizard.transfer_location_bootstrap_no_city') }}
                                </div>
                            @else
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span class="text-muted small me-1">{{ __('wizard.features_bulk_hint') }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="selectAllBootstrapCatalogLocations"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllBootstrapCatalogLocations,clearAllBootstrapCatalogLocations,applyTransferLocationBootstrapImport"
                                    >
                                        {{ __('wizard.features_select_all') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearAllBootstrapCatalogLocations"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllBootstrapCatalogLocations,clearAllBootstrapCatalogLocations,applyTransferLocationBootstrapImport"
                                    >
                                        {{ __('wizard.features_select_none') }}
                                    </button>
                                </div>

                                @if ($bootstrapModalLocations->isEmpty())
                                    <div class="alert alert-light border mb-0" role="alert">
                                        {{ __('wizard.transfer_location_bootstrap_no_locations_in_city') }}
                                    </div>
                                @else
                                    <p class="small text-muted mb-2">{{ __('wizard.transfer_location_bootstrap_select_locations') }}</p>
                                    <div class="row row-cols-1 row-cols-md-2 g-2">
                                        @foreach ($bootstrapModalLocations as $loc)
                                            <div class="col" wire:key="xfer-boot-loc-{{ $loc->id }}">
                                                <div class="border rounded bg-white p-3 h-100">
                                                    <div class="form-check m-0">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            id="xfer-boot-loc-{{ $loc->id }}"
                                                            value="{{ (string) $loc->id }}"
                                                            wire:model="bootstrapCatalogLocationIds"
                                                        >
                                                        <label class="form-check-label" for="xfer-boot-loc-{{ $loc->id }}">
                                                            <span class="fw-medium">{{ $loc->wizardRouteSelectLabel() }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            @error('bootstrapCatalogLocationIds')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                            @error('bootstrapLocationCityId')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" wire:click="dismissTransferLocationBootstrapModal">
                            {{ __('wizard.transfer_location_bootstrap_skip') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            wire:click="applyTransferLocationBootstrapImport"
                            wire:loading.attr="disabled"
                            wire:target="applyTransferLocationBootstrapImport"
                            @if ($locationBootstrapCityOptions === []) disabled @endif
                        >
                            <span wire:loading.remove wire:target="applyTransferLocationBootstrapImport">{{ __('wizard.transfer_location_bootstrap_import') }}</span>
                            <span wire:loading wire:target="applyTransferLocationBootstrapImport" class="spinner-border spinner-border-sm" role="status"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
