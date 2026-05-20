@php
    $isEdit = $priceList !== null;
    $packageItemOptions = $packageItemOptions ?? [];
    $itemPreviewUrl = $itemPreviewUrl ?? '';
    $itemsOld = old('items');
    $items = is_array($itemsOld)
        ? $itemsOld
        : ($isEdit ? $priceList->items->map(fn ($item) => [
            'operator_package_item_id' => $item->operator_package_item_id,
            'pricing_mode' => $item->pricing_mode,
            'price' => $item->price,
        ])->toArray() : [[
            'operator_package_item_id' => '',
            'pricing_mode' => 'direct',
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
                    <div class="page-title">
                        <h3 class="my-0">
                            {{ $isEdit ? __('account.operator_price_lists.edit_heading', ['name' => $priceList->name]) : __('account.operator_price_lists.create_heading') }}
                        </h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ __('account.operator_price_lists.form_intro', ['account' => $account->commercial_name ?? $account->name ?? $account->nick]) }}
                        </p>
                        @if ($isEdit)
                            <p class="mt-2 mb-0 small">
                                <a href="{{ route('account.operator-price-lists.assignments.edit', $priceList) }}">{{ __('account.operator_price_lists.assignments_link_from_edit') }}</a>
                            </p>
                        @endif
                    </div>
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
                                                <div class="mb-3">
                                                    <label for="valid_from" class="form-label">{{ __('account.price_lists.fields.valid_from') }}</label>
                                                    <input
                                                        id="valid_from"
                                                        name="valid_from"
                                                        type="date"
                                                        class="form-control @error('valid_from') is-invalid @enderror"
                                                        value="{{ old('valid_from', optional($priceList?->valid_from)->format('Y-m-d')) }}"
                                                    >
                                                    @error('valid_from')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="valid_to" class="form-label">{{ __('account.price_lists.fields.valid_to') }}</label>
                                                    <input
                                                        id="valid_to"
                                                        name="valid_to"
                                                        type="date"
                                                        class="form-control @error('valid_to') is-invalid @enderror"
                                                        value="{{ old('valid_to', optional($priceList?->valid_to)->format('Y-m-d')) }}"
                                                    >
                                                    @error('valid_to')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="mb-3 d-flex align-items-end h-100">
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
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="mb-0">{{ __('account.operator_price_lists.items_heading') }}</h5>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                                                {{ __('account.operator_price_lists.add_item_button') }}
                                            </button>
                                        </div>
                                        <p class="small text-muted mb-3">{{ __('account.operator_price_lists.items_help') }}</p>
                                        <p class="small text-muted mb-3">{{ __('account.operator_price_lists.items_target_help') }}</p>
                                        <p class="small text-muted mb-3">
                                            {{ __('account.price_lists.fields.price') }}: 12{{ $thousandsSeparator }}345{{ $decimalSeparator }}{{ str_repeat('0', max(1, $priceDecimals)) }}
                                        </p>

                                        @if ($packageItemOptions === [])
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
                                                            'packageItemOptions' => $packageItemOptions,
                                                            'priceStep' => $priceStep,
                                                        ])
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
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

    <template id="price-list-item-row-template">
        @include('account.operator-price-lists.partials.item-row', [
            'index' => '__INDEX__',
            'item' => ['operator_package_item_id' => '', 'pricing_mode' => 'direct', 'price' => ''],
            'packageItemOptions' => $packageItemOptions,
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
            const csrfToken = @json(csrf_token());

            if (!itemsBody || !addButton || !rowTemplate) {
                return;
            }

            const hintPercentage = @json(__('account.operator_price_lists.hints.percentage_on_provider_cost'));
            const hintFixedDelta = @json(__('account.operator_price_lists.hints.fixed_delta_on_provider_cost'));
            const hintDirect = @json(__('account.operator_price_lists.hints.direct_price'));
            const modeLabels = {
                percentage: @json(__('account.operator_price_lists.item_pricing_mode.percentage')),
                fixed_delta: @json(__('account.operator_price_lists.item_pricing_mode.fixed_delta')),
                direct: @json(__('account.operator_price_lists.item_pricing_mode.direct')),
            };

            let previewTimer = null;

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
                const priceHelp = row.querySelector('[data-role="item-price-help"]');
                if (!pricingModeSelect || !priceHelp) {
                    return;
                }

                const mode = pricingModeSelect.value;
                if (mode === 'percentage') {
                    priceHelp.textContent = hintPercentage;
                } else if (mode === 'fixed_delta') {
                    priceHelp.textContent = hintFixedDelta;
                } else {
                    priceHelp.textContent = hintDirect;
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
                    pricingModeSelect.value = allowedModes.includes('direct') ? 'direct' : allowedModes[0];
                }
            }

            function setRowPreview(row, data) {
                const providerCell = row.querySelector('[data-role="provider-cost-cell"]');
                const finalCell = row.querySelector('[data-role="final-price-cell"]');
                const warningBox = row.querySelector('[data-role="item-warning"]');

                if (providerCell) {
                    providerCell.textContent = data?.provider_cost_formatted ?? '—';
                }
                if (finalCell) {
                    finalCell.textContent = data?.final_price_formatted ?? '—';
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
                const pricingMode = row.querySelector('[data-role="pricing-mode-select"]')?.value ?? 'direct';
                const price = row.querySelector('[data-role="item-price"]')?.value ?? '';
                const currencyId = currencySelect?.value ?? '';

                if (!packageItemId || !currencyId) {
                    setRowPreview(row, {
                        provider_cost_formatted: '—',
                        final_price_formatted: '—',
                        warning: '',
                        allowed_modes: ['percentage', 'fixed_delta', 'direct'],
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
                window.clearTimeout(previewTimer);
                previewTimer = window.setTimeout(() => refreshRowPreview(row), 300);
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
            }

            addButton.addEventListener('click', addRow);

            itemsBody.querySelectorAll('[data-role="price-list-item-row"]').forEach((row) => bindRowEvents(row));

            if (currencySelect) {
                currencySelect.addEventListener('change', () => {
                    itemsBody.querySelectorAll('[data-role="price-list-item-row"]').forEach((row) => scheduleRowPreview(row));
                });
            }

            itemsBody.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement) || !target.classList.contains('btn-remove-item')) {
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
