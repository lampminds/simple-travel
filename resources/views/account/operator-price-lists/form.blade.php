@php
    $isEdit = $priceList !== null;
    $packageItemCatalog = $packageItemCatalog ?? [];
    $packageItemCatalogById = [];
    foreach ($packageItemCatalog as $catalogRecord) {
        $packageItemCatalogById[(string) ($catalogRecord['id'] ?? '')] = $catalogRecord;
    }
    $itemPreviewUrl = $itemPreviewUrl ?? '';
    $catalogCostsUrl = $catalogCostsUrl ?? '';
    $itemsOld = old('items');
    $items = is_array($itemsOld)
        ? $itemsOld
        : ($isEdit ? $priceList->items->map(fn ($item) => [
            'operator_package_item_id' => $item->operator_package_item_id,
            'pricing_mode' => $item->pricing_mode,
            'price' => $item->price,
        ])->toArray() : [[
            'operator_package_item_id' => '',
            'pricing_mode' => '',
            'price' => '',
        ]]);
    $activePriceListTab = 'general';
    $priceDecimals = max(0, min(3, (int) ($priceFormatSettings['decimals'] ?? 2)));
    $priceStep = $priceDecimals === 0 ? '1' : '0.'.str_repeat('0', $priceDecimals - 1).'1';
    $thousandsSeparator = (string) ($priceFormatSettings['thousands_separator'] ?? ',');
    $decimalSeparator = (string) ($priceFormatSettings['decimal_separator'] ?? '.');
    if ($errors->any()) {
        foreach (array_keys($errors->getMessages()) as $errorKey) {
            $errorKey = (string) $errorKey;
            if ($errorKey === 'items' || str_starts_with($errorKey, 'items.')) {
                $activePriceListTab = 'items';
                break;
            }
        }
    }
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.operator_price_lists.edit_page_title') : __('account.operator_price_lists.create_page_title')])

@section('css')
    <style>
        .package-item-value-proxy {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1px;
            margin: 0;
            padding: 0;
            border: 0;
            opacity: 0;
            pointer-events: none;
        }

        .package-item-picker-scroll {
            max-height: 60vh;
            overflow: auto;
        }

        #package-item-picker-table {
            table-layout: fixed;
            width: 100%;
        }

        #package-item-picker-table td {
            vertical-align: top;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        #package-item-picker-table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            white-space: nowrap;
        }

        #package-item-picker-table .picker-col-sku,
        #package-item-picker-table .picker-col-schedule,
        #package-item-picker-table .picker-col-day,
        #package-item-picker-table .picker-col-cost {
            white-space: nowrap;
        }

        #package-item-picker-table tbody tr.is-available {
            cursor: pointer;
        }

        #package-item-picker-table tbody tr.is-available:hover {
            background: rgba(13, 110, 253, 0.06);
        }
    </style>
@endsection

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="$isEdit ? __('account.operator_price_lists.edit_title') : __('account.operator_price_lists.create_heading')"
                        :subtitle="$isEdit ? $priceList->name : ($account->commercial_name ?? $account->name ?? $account->nick)"
                        :instructions="__('account.operator_price_lists.form_instructions')"
                    >
                        @if ($isEdit)
                            <p class="mt-2 mb-0 small">
                                <a href="{{ route('account.operator-price-lists.assignments.edit', $priceList) }}">{{ __('account.operator_price_lists.assignments_link_from_edit') }}</a>
                            </p>
                        @endif
                    </x-account-page-header>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ $submitRoute }}">
                                @csrf
                                @if ($submitMethod !== 'POST')
                                    @method($submitMethod)
                                @endif

                                <ul class="nav nav-tabs flex-wrap mb-3" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link @if ($activePriceListTab === 'general') active @endif"
                                            id="price-list-tab-general-link"
                                            data-bs-toggle="tab"
                                            href="#price-list-tab-general"
                                            role="tab"
                                            aria-controls="price-list-tab-general"
                                            aria-selected="{{ $activePriceListTab === 'general' ? 'true' : 'false' }}"
                                        >{{ __('account.price_lists.tab_general') }}</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link @if ($activePriceListTab === 'items') active @endif"
                                            id="price-list-tab-items-link"
                                            data-bs-toggle="tab"
                                            href="#price-list-tab-items"
                                            role="tab"
                                            aria-controls="price-list-tab-items"
                                            aria-selected="{{ $activePriceListTab === 'items' ? 'true' : 'false' }}"
                                        >{{ __('account.price_lists.tab_items') }}</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div
                                        class="tab-pane fade @if ($activePriceListTab === 'general') show active @endif"
                                        id="price-list-tab-general"
                                        role="tabpanel"
                                        aria-labelledby="price-list-tab-general-link"
                                    >
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">{{ __('account.price_lists.fields.name') }}</label>
                                                    <input
                                                        id="name"
                                                        name="name"
                                                        type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        maxlength="255"
                                                        required
                                                        value="{{ old('name', $priceList->name ?? '') }}"
                                                    >
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="currency_id" class="form-label">{{ __('account.price_lists.fields.currency') }}</label>
                                                    <select id="currency_id" name="currency_id" class="form-select @error('currency_id') is-invalid @enderror" required>
                                                        <option value="">{{ __('account.price_lists.fields.currency_placeholder') }}</option>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency->id }}" @selected((string) old('currency_id', $priceList->currency_id ?? '') === (string) $currency->id)>
                                                                {{ $currency->display_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('currency_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="mb-3 d-flex align-items-end">
                                                    <div class="form-check form-switch mb-2">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            id="is_active"
                                                            name="is_active"
                                                            value="1"
                                                            @checked((bool) old('is_active', $priceList->is_active ?? true))
                                                        >
                                                        <label class="form-check-label" for="is_active">{{ __('account.price_lists.fields.is_active') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="tab-pane fade @if ($activePriceListTab === 'items') show active @endif"
                                        id="price-list-tab-items"
                                        role="tabpanel"
                                        aria-labelledby="price-list-tab-items-link"
                                    >
                                        <h5 class="mb-2">{{ __('account.operator_price_lists.items_heading') }}</h5>
                                        <p class="small text-muted mb-3">{{ __('account.operator_price_lists.items_help') }}</p>
                                        <p class="small text-muted mb-3">{{ __('account.operator_price_lists.items_target_help') }}</p>
                                        <p class="small text-muted mb-3">
                                            {{ __('account.price_lists.fields.price') }}: 12{{ $thousandsSeparator }}345{{ $decimalSeparator }}{{ str_repeat('0', max(1, $priceDecimals)) }}
                                        </p>

                                        @if ($packageItemCatalog === [])
                                            <div class="alert alert-info mb-3">{{ __('account.operator_price_lists.no_package_items') }}</div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table align-middle" id="items-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('account.operator_price_lists.fields.package_item') }}</th>
                                                        <th>{{ __('account.price_lists.fields.pricing_mode') }}</th>
                                                        <th>{{ __('account.operator_price_lists.fields.list_item_value') }}</th>
                                                        <th class="text-end">{{ __('account.operator_price_lists.columns.provider_cost') }}</th>
                                                        <th class="text-end">{{ __('account.operator_price_lists.columns.final_price') }}</th>
                                                        <th class="text-end">{{ __('account.price_lists.fields.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="items-body">
                                                    @foreach ($items as $index => $item)
                                                        @include('account.operator-price-lists.partials.item-row', [
                                                            'index' => $index,
                                                            'item' => $item,
                                                            'packageItemCatalogById' => $packageItemCatalogById,
                                                            'priceStep' => $priceStep,
                                                        ])
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-add-item">
                                            {{ __('account.operator_price_lists.add_item_button') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.operator_price_lists.cancel_button') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('account.operator_price_lists.update_button') : __('account.operator_price_lists.save_button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div
        class="modal fade"
        id="package-item-picker-modal"
        tabindex="-1"
        aria-labelledby="package-item-picker-title"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl my-3 mx-auto">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="package-item-picker-title">{{ __('account.operator_price_lists.picker.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('account.operator_price_lists.picker.close') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">{{ __('account.operator_price_lists.picker.help') }}</p>
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-lg-8">
                            <input
                                type="search"
                                id="package-item-picker-search"
                                class="form-control"
                                placeholder="{{ __('account.operator_price_lists.picker.search_placeholder') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <span class="small text-muted" id="package-item-picker-count"></span>
                        </div>
                    </div>
                    <p class="small text-muted mb-2" id="package-item-picker-cost-status"></p>
                    <div class="package-item-picker-scroll border rounded">
                        <table class="table table-sm align-middle mb-0" id="package-item-picker-table">
                            <colgroup>
                                <col style="width: 16%">
                                <col style="width: 10%">
                                <col style="width: 22%">
                                <col style="width: 9%">
                                <col style="width: 6%">
                                <col style="width: 9%">
                                <col style="width: 14%">
                                <col style="width: 12%">
                                <col style="width: 12%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>{{ __('account.operator_price_lists.picker.columns.package') }}</th>
                                    <th class="picker-col-sku">{{ __('account.operator_price_lists.picker.columns.sku') }}</th>
                                    <th>{{ __('account.operator_price_lists.picker.columns.service') }}</th>
                                    <th class="picker-col-schedule">{{ __('account.operator_price_lists.picker.columns.schedule') }}</th>
                                    <th class="picker-col-day">{{ __('account.operator_price_lists.picker.columns.day') }}</th>
                                    <th>{{ __('account.operator_price_lists.picker.columns.inclusion') }}</th>
                                    <th>{{ __('account.operator_price_lists.picker.columns.provider') }}</th>
                                    <th class="text-end picker-col-cost">{{ __('account.operator_price_lists.picker.columns.cost') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="package-item-picker-body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('account.operator_price_lists.picker.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <template id="price-list-item-row-template">
        @include('account.operator-price-lists.partials.item-row', [
            'index' => '__INDEX__',
            'item' => ['operator_package_item_id' => '', 'pricing_mode' => '', 'price' => ''],
            'packageItemCatalogById' => $packageItemCatalogById,
            'priceStep' => $priceStep,
        ])
    </template>

    <script>
        (function () {
            const itemsBody = document.getElementById('items-body');
            const addButton = document.getElementById('btn-add-item');
            const rowTemplate = document.getElementById('price-list-item-row-template');
            const currencySelect = document.getElementById('currency_id');
            const previewUrl = @json($itemPreviewUrl);
            const catalogCostsUrl = @json($catalogCostsUrl);
            const csrfToken = @json(csrf_token());
            const packageCatalog = @json(array_values($packageItemCatalog));
            const catalogById = new Map(packageCatalog.map((item) => [String(item.id), item]));
            const pickerLabels = {
                open: @json(__('account.operator_price_lists.picker.open')),
                change: @json(__('account.operator_price_lists.picker.change')),
                choose: @json(__('account.operator_price_lists.picker.choose')),
                alreadyUsed: @json(__('account.operator_price_lists.picker.already_used')),
                empty: @json(__('account.operator_price_lists.picker.empty')),
                placeholder: @json(__('account.operator_price_lists.fields.package_item_placeholder')),
                costNeedsCurrency: @json(__('account.operator_price_lists.picker.cost_needs_currency')),
                loadingCosts: @json(__('account.operator_price_lists.picker.loading_costs')),
                costPrefix: @json(__('account.operator_price_lists.picker.cost_prefix')),
                resultsCount: @json(__('account.operator_price_lists.picker.results_count')),
                missingLine: @json(__('account.operator_price_lists.picker.missing_line')),
                schedule: @json(__('account.operator_price_lists.picker.columns.schedule')),
            };

            if (!itemsBody || !addButton || !rowTemplate) {
                return;
            }

            const hintPercentage = @json(__('account.operator_price_lists.hints.percentage_on_provider_cost'));
            const hintFixedDelta = @json(__('account.operator_price_lists.hints.fixed_delta_on_provider_cost'));
            const hintFixedPrice = @json(__('account.operator_price_lists.hints.fixed_price'));
            const hintProviderCost = @json(__('account.operator_price_lists.hints.provider_cost'));
            const providerCostUnavailable = @json(__('account.operator_price_lists.provider_cost_unavailable'));
            const modeProviderCost = '';
            const modeFixedPrice = 'fixed_price';

            const previewTimers = new WeakMap();
            const costCache = new Map();
            let activeCosts = null;
            let costRequestSerial = 0;
            let activePickerRow = null;
            let pickerModal = null;

            function rowsCount() {
                return itemsBody.querySelectorAll('[data-role="price-list-item-row"]').length;
            }

            function refreshRowIndexes() {
                itemsBody.querySelectorAll('[data-role="price-list-item-row"]').forEach((row, index) => {
                    row.querySelectorAll('[name]').forEach((input) => {
                        const currentName = input.getAttribute('name');
                        if (!currentName) {
                            return;
                        }
                        input.setAttribute('name', currentName.replace(/items\[[^\]]+\]/, 'items[' + index + ']'));
                    });
                });
            }

            function applyPricingBehavior(row) {
                const pricingModeSelect = row.querySelector('[data-role="pricing-mode-select"]');
                const priceInput = row.querySelector('[data-role="item-price"]');
                const priceDisplay = row.querySelector('[data-role="item-price-provider-cost"]');
                const priceHelp = row.querySelector('[data-role="item-price-help"]');
                if (!pricingModeSelect || !priceInput || !priceHelp) {
                    return;
                }

                const mode = pricingModeSelect.value;
                const isProviderCost = mode === modeProviderCost;

                if (isProviderCost) {
                    priceInput.value = '';
                    priceInput.classList.add('d-none');
                    priceInput.removeAttribute('required');
                    priceHelp.textContent = '';
                    priceHelp.classList.add('d-none');

                    if (priceDisplay instanceof HTMLElement) {
                        priceDisplay.classList.remove('d-none');
                        priceDisplay.removeAttribute('aria-hidden');
                    }
                } else {
                    priceInput.classList.remove('d-none');
                    priceHelp.classList.remove('d-none');

                    if (mode === 'percentage') {
                        priceHelp.textContent = hintPercentage;
                    } else if (mode === 'fixed_delta') {
                        priceHelp.textContent = hintFixedDelta;
                    } else {
                        priceHelp.textContent = hintFixedPrice;
                    }

                    if (priceDisplay instanceof HTMLElement) {
                        priceDisplay.textContent = '';
                        priceDisplay.classList.add('d-none');
                        priceDisplay.setAttribute('aria-hidden', 'true');
                    }
                }
            }

            function syncModeOptions(row, allowedModes) {
                const pricingModeSelect = row.querySelector('[data-role="pricing-mode-select"]');
                if (!pricingModeSelect || !Array.isArray(allowedModes)) {
                    return;
                }

                const current = pricingModeSelect.value;
                pricingModeSelect.querySelectorAll('option').forEach((option) => {
                    const enabled = allowedModes.includes(option.value);
                    option.disabled = !enabled;
                    option.hidden = !enabled;
                });

                if (!allowedModes.includes(current)) {
                    pricingModeSelect.value = allowedModes.includes(modeFixedPrice) ? modeFixedPrice : allowedModes[0];
                }
            }

            function setRowPreview(row, data) {
                const providerCell = row.querySelector('[data-role="provider-cost-cell"]');
                const finalCell = row.querySelector('[data-role="final-price-cell"]');
                const warningBox = row.querySelector('[data-role="item-warning"]');
                const priceDisplay = row.querySelector('[data-role="item-price-provider-cost"]');
                const pricingMode = row.querySelector('[data-role="pricing-mode-select"]')?.value ?? modeProviderCost;

                if (providerCell) {
                    providerCell.textContent = data?.provider_cost_formatted ?? '—';
                }
                if (finalCell) {
                    finalCell.textContent = data?.final_price_formatted ?? '—';
                }
                if (priceDisplay instanceof HTMLElement && pricingMode === modeProviderCost) {
                    priceDisplay.textContent = data?.provider_cost_has
                        ? data.provider_cost_formatted
                        : providerCostUnavailable;
                }
                const summaryCost = row.querySelector('[data-role="package-item-summary-cost"]');
                if (summaryCost) {
                    const packageItemId = row.querySelector('[data-role="package-item-select"]')?.value ?? '';
                    summaryCost.textContent = packageItemId && data?.provider_cost_has
                        ? pickerLabels.costPrefix + ': ' + data.provider_cost_formatted
                        : '';
                }
                if (warningBox) {
                    const warning = data?.warning ?? '';
                    warningBox.textContent = warning;
                    warningBox.classList.toggle('d-none', warning === '');
                }

                if (data?.allowed_modes) {
                    syncModeOptions(row, data.allowed_modes);
                }
                applyPricingBehavior(row);
            }

            async function refreshRowPreview(row) {
                const packageItemId = row.querySelector('[data-role="package-item-select"]')?.value ?? '';
                const pricingMode = row.querySelector('[data-role="pricing-mode-select"]')?.value ?? modeProviderCost;
                const price = row.querySelector('[data-role="item-price"]')?.value ?? '';
                const currencyId = currencySelect?.value ?? '';

                if (!packageItemId || !currencyId) {
                    setRowPreview(row, {
                        provider_cost_formatted: '—',
                        final_price_formatted: '—',
                        warning: '',
                        allowed_modes: ['', 'percentage', 'fixed_delta', 'fixed_price'],
                    });

                    return;
                }

                try {
                    const response = await fetch(previewUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            operator_package_item_id: packageItemId,
                            currency_id: currencyId,
                            pricing_mode: pricingMode,
                            price: price,
                        }),
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    setRowPreview(row, data);
                } catch (error) {
                    console.error(error);
                }
            }

            function scheduleRowPreview(row) {
                window.clearTimeout(previewTimers.get(row));
                previewTimers.set(row, window.setTimeout(() => refreshRowPreview(row), 300));
            }

            function element(tag, className, text) {
                const node = document.createElement(tag);
                if (className) {
                    node.className = className;
                }
                if (text !== undefined && text !== null && text !== '') {
                    node.textContent = String(text);
                }

                return node;
            }

            function renderSummary(container, item, fallbackId) {
                container.replaceChildren();

                if (!item && !fallbackId) {
                    container.appendChild(element('span', 'text-muted', pickerLabels.placeholder));
                    return;
                }

                if (!item) {
                    container.appendChild(element('div', 'fw-semibold', pickerLabels.missingLine.replace(':id', fallbackId)));
                } else {
                    container.appendChild(element('div', 'fw-semibold', item.package_name));
                    const serviceLine = [item.sku, item.service_name].filter(Boolean).join(' — ');
                    if (serviceLine) {
                        container.appendChild(element('div', '', serviceLine));
                    }
                    if (item.variant_name) {
                        container.appendChild(element('div', 'text-muted', item.variant_name));
                    }
                    const meta = [
                        item.day_label,
                        item.inclusion,
                        item.quantity > 1 ? '×' + item.quantity : '',
                        item.provider,
                    ].filter(Boolean).join(' · ');
                    if (meta) {
                        container.appendChild(element('div', 'text-muted small', meta));
                    }
                    if (item.schedule) {
                        container.appendChild(element('div', 'small', pickerLabels.schedule + ': ' + item.schedule));
                    }
                }

                const cost = element('div', 'small fw-semibold');
                cost.dataset.role = 'package-item-summary-cost';
                container.appendChild(cost);
            }

            function syncPickerButton(row) {
                const id = row.querySelector('[data-role="package-item-select"]')?.value ?? '';
                const button = row.querySelector('[data-role="open-package-item-picker"]');
                if (button) {
                    button.textContent = id ? pickerLabels.change : pickerLabels.open;
                }
                if (id) {
                    row.querySelector('[data-role="package-item-picker"]')?.classList.remove('border-danger');
                }
            }

            function usedPackageItemIds(exceptRow) {
                const ids = new Set();
                itemsBody.querySelectorAll('[data-role="price-list-item-row"]').forEach((row) => {
                    if (row === exceptRow) {
                        return;
                    }
                    const id = row.querySelector('[data-role="package-item-select"]')?.value ?? '';
                    if (id) {
                        ids.add(String(id));
                    }
                });

                return ids;
            }

            function pickerCostText(id) {
                if (!(currencySelect?.value)) {
                    return '—';
                }
                if (!activeCosts) {
                    return '…';
                }
                const entry = activeCosts[String(id)];

                return entry?.has ? entry.formatted : '—';
            }

            function setCostStatus(message) {
                const status = document.getElementById('package-item-picker-cost-status');
                if (status) {
                    status.textContent = message;
                }
            }

            function renderPickerTable() {
                const tbody = document.getElementById('package-item-picker-body');
                const countEl = document.getElementById('package-item-picker-count');
                const searchInput = document.getElementById('package-item-picker-search');
                if (!tbody) {
                    return;
                }

                const query = (searchInput?.value ?? '').trim().toLocaleLowerCase();
                const used = usedPackageItemIds(activePickerRow);
                const currentId = String(activePickerRow?.querySelector('[data-role="package-item-select"]')?.value ?? '');
                const visible = packageCatalog.filter((item) => query === '' || String(item.search).includes(query));

                if (countEl) {
                    countEl.textContent = pickerLabels.resultsCount.replace(':count', String(visible.length));
                }

                tbody.replaceChildren();

                if (visible.length === 0) {
                    const row = document.createElement('tr');
                    const cell = element('td', 'text-muted p-3', pickerLabels.empty);
                    cell.colSpan = 9;
                    row.appendChild(cell);
                    tbody.appendChild(row);
                    return;
                }

                visible.forEach((item) => {
                    const id = String(item.id);
                    const taken = used.has(id);
                    const row = document.createElement('tr');
                    row.dataset.id = id;
                    row.classList.add(taken ? 'text-muted' : 'is-available');

                    const packageCell = document.createElement('td');
                    packageCell.appendChild(element('div', 'text-muted small', item.kind));
                    packageCell.appendChild(element('div', 'fw-semibold', item.package_name));
                    row.appendChild(packageCell);
                    row.appendChild(element('td', 'picker-col-sku', item.sku || '—'));

                    const serviceCell = document.createElement('td');
                    if (item.service_name) {
                        serviceCell.appendChild(element('div', '', item.service_name));
                    }
                    if (item.variant_name) {
                        serviceCell.appendChild(element('div', 'text-muted', item.variant_name));
                    }
                    row.appendChild(serviceCell);
                    row.appendChild(element('td', 'picker-col-schedule', item.schedule || '—'));
                    row.appendChild(element('td', 'picker-col-day', item.day_number === null || item.day_number === undefined ? '—' : String(item.day_number)));

                    const inclusionCell = document.createElement('td');
                    inclusionCell.appendChild(element('div', '', item.inclusion || '—'));
                    if (item.quantity > 1) {
                        inclusionCell.appendChild(element('div', 'text-muted small', '×' + item.quantity));
                    }
                    row.appendChild(inclusionCell);
                    row.appendChild(element('td', '', item.provider || '—'));
                    row.appendChild(element('td', 'text-end picker-col-cost fw-semibold', pickerCostText(id)));

                    const actionCell = element('td', 'text-end');
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-sm ' + (taken ? 'btn-outline-secondary' : 'btn-primary');
                    button.dataset.role = 'picker-choose';
                    button.dataset.id = id;
                    button.disabled = taken;
                    button.textContent = taken
                        ? pickerLabels.alreadyUsed
                        : (id === currentId ? pickerLabels.change : pickerLabels.choose);
                    actionCell.appendChild(button);
                    row.appendChild(actionCell);
                    tbody.appendChild(row);
                });
            }

            async function loadPickerCosts(currencyId) {
                const requestId = ++costRequestSerial;
                setCostStatus(pickerLabels.loadingCosts);

                try {
                    const response = await fetch(catalogCostsUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ currency_id: Number(currencyId) }),
                    });

                    if (!response.ok) {
                        throw new Error('catalog costs');
                    }

                    const data = await response.json();
                    const costs = data.costs && !Array.isArray(data.costs) ? data.costs : {};
                    costCache.set(String(currencyId), costs);

                    if (requestId !== costRequestSerial || String(currencySelect?.value ?? '') !== String(currencyId)) {
                        return;
                    }

                    activeCosts = costs;
                    setCostStatus('');
                    renderPickerTable();
                } catch (error) {
                    console.error(error);
                    if (requestId !== costRequestSerial) {
                        return;
                    }
                    activeCosts = {};
                    setCostStatus('');
                    renderPickerTable();
                }
            }

            function ensurePickerCosts() {
                const currencyId = currencySelect?.value ?? '';
                if (!currencyId) {
                    activeCosts = null;
                    setCostStatus(pickerLabels.costNeedsCurrency);
                    renderPickerTable();
                    return;
                }

                if (costCache.has(String(currencyId))) {
                    activeCosts = costCache.get(String(currencyId));
                    setCostStatus('');
                    renderPickerTable();
                    return;
                }

                activeCosts = null;
                renderPickerTable();
                loadPickerCosts(currencyId);
            }

            function openPicker(row) {
                const modalElement = document.getElementById('package-item-picker-modal');
                if (!modalElement || !window.bootstrap || !window.bootstrap.Modal) {
                    return;
                }

                activePickerRow = row;
                const searchInput = document.getElementById('package-item-picker-search');
                if (searchInput instanceof HTMLInputElement) {
                    searchInput.value = '';
                }
                pickerModal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
                ensurePickerCosts();
                pickerModal.show();
            }

            function choosePackageItem(id) {
                if (!activePickerRow) {
                    return;
                }

                const input = activePickerRow.querySelector('[data-role="package-item-select"]');
                const summary = activePickerRow.querySelector('[data-role="package-item-summary"]');
                if (!(input instanceof HTMLInputElement) || !summary) {
                    return;
                }

                input.value = String(id);
                renderSummary(summary, catalogById.get(String(id)) ?? null, String(id));
                syncPickerButton(activePickerRow);
                input.dispatchEvent(new Event('change', { bubbles: true }));
                pickerModal?.hide();
            }

            function bindRowEvents(row) {
                row.querySelectorAll('[data-role="package-item-select"], [data-role="pricing-mode-select"], [data-role="item-price"]').forEach((el) => {
                    el.addEventListener('change', () => scheduleRowPreview(row));
                    el.addEventListener('input', () => scheduleRowPreview(row));
                });
                applyPricingBehavior(row);
                scheduleRowPreview(row);
            }

            function addRow() {
                const index = rowsCount();
                const html = rowTemplate.innerHTML.replaceAll('__INDEX__', String(index));
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = html.trim();
                const row = wrapper.querySelector('tr');
                if (!row) {
                    return;
                }
                itemsBody.appendChild(row);
                bindRowEvents(row);
                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            addButton.addEventListener('click', addRow);

            itemsBody.querySelectorAll('[data-role="price-list-item-row"]').forEach((row) => bindRowEvents(row));

            if (currencySelect) {
                currencySelect.addEventListener('change', () => {
                    itemsBody.querySelectorAll('[data-role="price-list-item-row"]').forEach((row) => scheduleRowPreview(row));
                    const modalElement = document.getElementById('package-item-picker-modal');
                    if (modalElement?.classList.contains('show')) {
                        ensurePickerCosts();
                    }
                });
            }

            const pickerSearch = document.getElementById('package-item-picker-search');
            if (pickerSearch) {
                pickerSearch.addEventListener('input', () => renderPickerTable());
                pickerSearch.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });
            }

            const pickerBody = document.getElementById('package-item-picker-body');
            if (pickerBody) {
                pickerBody.addEventListener('click', (event) => {
                    const target = event.target;
                    if (!(target instanceof HTMLElement)) {
                        return;
                    }
                    const row = target.closest('tr');
                    const button = row?.querySelector('[data-role="picker-choose"]');
                    if (!(button instanceof HTMLButtonElement) || button.disabled) {
                        return;
                    }
                    choosePackageItem(button.dataset.id ?? '');
                });
            }

            const pickerModalElement = document.getElementById('package-item-picker-modal');
            if (pickerModalElement) {
                pickerModalElement.addEventListener('shown.bs.modal', () => {
                    document.getElementById('package-item-picker-search')?.focus();
                });
            }

            itemsBody.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement)) {
                    return;
                }

                const pickerButton = target.closest('[data-role="open-package-item-picker"]');
                if (pickerButton) {
                    const row = pickerButton.closest('tr');
                    if (row) {
                        openPicker(row);
                    }
                    return;
                }

                if (!target.classList.contains('btn-remove-item')) {
                    return;
                }

                const row = target.closest('tr');
                if (!row || rowsCount() <= 1) {
                    return;
                }

                row.remove();
                refreshRowIndexes();
            });

            const form = itemsBody.closest('form');
            if (form) {
                form.addEventListener('invalid', function (event) {
                    const target = event.target;
                    if (!(target instanceof HTMLElement)) {
                        return;
                    }
                    const pane = target.closest('.tab-pane');
                    if (!pane || !pane.id) {
                        return;
                    }
                    if (target.matches('[data-role="package-item-select"]')) {
                        target.closest('td')?.querySelector('[data-role="package-item-picker"]')?.classList.add('border-danger');
                    }
                    const tabTrigger = document.querySelector('a.nav-link[href="#' + pane.id + '"]');
                    if (tabTrigger && window.bootstrap && window.bootstrap.Tab) {
                        const Tab = window.bootstrap.Tab;
                        const inst = Tab.getInstance(tabTrigger) ?? new Tab(tabTrigger);
                        inst.show();
                    }
                }, true);
            }
        })();
    </script>

    <x-site-footer-simple />

@endsection
