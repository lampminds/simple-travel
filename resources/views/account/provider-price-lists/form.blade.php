@php
    $isEdit = $priceList !== null;
    $serviceOptions = $serviceOptions ?? [];
    $itemsOld = old('items');
    $items = is_array($itemsOld)
        ? $itemsOld
        : ($isEdit ? $priceList->items->map(fn ($item) => [
            'service_id' => $item->service_id,
            'service_variant_id' => $item->service_variant_id,
            'pricing_mode' => $item->pricing_mode,
            'price' => $item->price,
        ])->toArray() : [[
            'service_id' => '',
            'service_variant_id' => '',
            'pricing_mode' => 'fixed',
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

@extends('layouts.base', ['title' => $isEdit ? __('account.price_lists.edit_page_title') : __('account.price_lists.create_page_title')])

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
                            {{ $isEdit ? __('account.price_lists.edit_heading', ['name' => $priceList->name]) : __('account.price_lists.create_heading') }}
                        </h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ __('account.price_lists.form_intro', ['account' => $account->commercial_name ?? $account->name ?? $account->nick]) }}
                        </p>
                        @if ($isEdit)
                            <p class="mt-2 mb-0 small">
                                <a href="{{ route('account.provider-price-lists.assignments.edit', $priceList) }}">{{ __('account.price_lists.assignments_link_from_edit') }}</a>
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
                                            <h5 class="mb-0">{{ __('account.price_lists.items_heading') }}</h5>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                                                {{ __('account.price_lists.add_item_button') }}
                                            </button>
                                        </div>
                                        <p class="small text-muted mb-3">{{ __('account.price_lists.items_help') }}</p>
                                        <p class="small text-muted mb-3">{{ __('account.price_lists.items_target_help') }}</p>
                                        <p class="small text-muted mb-3">
                                            {{ __('account.price_lists.fields.price') }}: 12{{ $thousandsSeparator }}345{{ $decimalSeparator }}{{ str_repeat('0', max(1, $priceDecimals)) }}
                                        </p>

                                        <div class="table-responsive">
                                            <table class="table align-middle" id="items-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('account.price_lists.fields.service_all_variants') }}</th>
                                                        <th>{{ __('account.price_lists.fields.variant_specific') }}</th>
                                                        <th>{{ __('account.price_lists.fields.pricing_mode') }}</th>
                                                        <th>{{ __('account.price_lists.fields.price') }}</th>
                                                        <th class="text-end">{{ __('account.price_lists.fields.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="items-body">
                                                    @foreach($items as $index => $item)
                                                        <tr>
                                                            <td>
                                                                <select name="items[{{ $index }}][service_id]" class="form-select" data-role="item-service">
                                                                    <option value="">{{ __('account.price_lists.fields.service_placeholder') }}</option>
                                                                    @foreach($serviceOptions as $serviceId => $serviceLabel)
                                                                        <option value="{{ $serviceId }}" @selected((string) ($item['service_id'] ?? '') === (string) $serviceId)>
                                                                            {{ $serviceLabel }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="items[{{ $index }}][service_variant_id]" class="form-select" data-role="item-variant">
                                                                    <option value="">{{ __('account.price_lists.fields.variant_placeholder') }}</option>
                                                                    @foreach($serviceVariantOptions as $variantId => $variantLabel)
                                                                        <option value="{{ $variantId }}" @selected((string) ($item['service_variant_id'] ?? '') === (string) $variantId)>
                                                                            {{ $variantLabel }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="items[{{ $index }}][pricing_mode]" class="form-select" required>
                                                                    <option value="fixed" @selected(($item['pricing_mode'] ?? '') === 'fixed')>{{ __('account.price_lists.pricing_mode.fixed') }}</option>
                                                                    <option value="percentage" @selected(($item['pricing_mode'] ?? '') === 'percentage')>{{ __('account.price_lists.pricing_mode.percentage') }}</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input
                                                                    type="number"
                                                                    name="items[{{ $index }}][price]"
                                                                    class="form-control"
                                                                    data-role="item-price"
                                                                    step="{{ $priceStep }}"
                                                                    required
                                                                    value="{{ $item['price'] ?? '' }}"
                                                                >
                                                                <small class="form-text text-muted" data-role="item-price-help"></small>
                                                            </td>
                                                            <td class="text-end">
                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                                                    {{ __('account.price_lists.remove_item_button') }}
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.price_lists.cancel_button') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('account.price_lists.update_button') : __('account.price_lists.save_button') }}
                                    </button>
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
            const itemsBody = document.getElementById('items-body');
            const addButton = document.getElementById('btn-add-item');
            if (!itemsBody || !addButton) {
                return;
            }

            const variantOptionsHtml = @json(collect($serviceVariantOptions)->map(
                fn ($label, $id) => '<option value="'.e((string) $id).'">'.e($label).'</option>'
            )->implode(''));

            const serviceOptionsHtml = @json(collect($serviceOptions)->map(
                fn ($label, $id) => '<option value="'.e((string) $id).'">'.e($label).'</option>'
            )->implode(''));

            const servicePlaceholder = @json(__('account.price_lists.fields.service_placeholder'));
            const variantPlaceholder = @json(__('account.price_lists.fields.variant_placeholder'));
            function rowsCount() {
                return itemsBody.querySelectorAll('tr').length;
            }

            function refreshRowIndexes() {
                const rows = itemsBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    row.querySelectorAll('[name]').forEach((input) => {
                        const currentName = input.getAttribute('name');
                        if (!currentName) {
                            return;
                        }
                        const updatedName = currentName.replace(/items\[\d+\]/, 'items[' + index + ']');
                        input.setAttribute('name', updatedName);
                    });
                });
            }

            const amountPlaceholder = @json(__('account.price_lists.fields.amount_placeholder'));
            const percentagePlaceholder = @json(__('account.price_lists.fields.percentage_placeholder'));
            const hintFixedList = @json(__('account.price_lists.hints.fixed_list_price'));
            const hintPercentageBase = @json(__('account.price_lists.hints.percentage_on_base'));
            const priceStep = @json($priceStep);
            const modePercentage = 'percentage';

            function applyPricingBehavior(row) {
                const pricingModeSelect = row.querySelector('select[name$="[pricing_mode]"]');
                const priceInput = row.querySelector('[data-role="item-price"]');
                const priceHelp = row.querySelector('[data-role="item-price-help"]');

                if (!pricingModeSelect || !priceInput || !priceHelp) {
                    return;
                }

                const isPercentage = pricingModeSelect.value === modePercentage;

                priceInput.placeholder = isPercentage ? percentagePlaceholder : amountPlaceholder;
                priceHelp.textContent = isPercentage ? hintPercentageBase : hintFixedList;
            }

            function bindServiceVariantExclusivity(row) {
                const serviceSelect = row.querySelector('[data-role="item-service"]');
                const variantSelect = row.querySelector('[data-role="item-variant"]');

                if (!serviceSelect || !variantSelect) {
                    return;
                }

                serviceSelect.addEventListener('change', () => {
                    if (serviceSelect.value) {
                        variantSelect.value = '';
                    }
                });

                variantSelect.addEventListener('change', () => {
                    if (variantSelect.value) {
                        serviceSelect.value = '';
                    }
                });
            }

            function bindRowEvents(row) {
                const pricingModeSelect = row.querySelector('select[name$="[pricing_mode]"]');

                if (pricingModeSelect) {
                    pricingModeSelect.addEventListener('change', () => applyPricingBehavior(row));
                }

                bindServiceVariantExclusivity(row);
                applyPricingBehavior(row);
            }

            function addRow() {
                const index = rowsCount();
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <select name="items[${index}][service_id]" class="form-select" data-role="item-service">
                            <option value="">${servicePlaceholder}</option>
                            ${serviceOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <select name="items[${index}][service_variant_id]" class="form-select" data-role="item-variant">
                            <option value="">${variantPlaceholder}</option>
                            ${variantOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <select name="items[${index}][pricing_mode]" class="form-select" required>
                            <option value="fixed">{{ __('account.price_lists.pricing_mode.fixed') }}</option>
                            <option value="percentage">{{ __('account.price_lists.pricing_mode.percentage') }}</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][price]" class="form-control" data-role="item-price" step="${priceStep}" required>
                        <small class="form-text text-muted" data-role="item-price-help"></small>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                            {{ __('account.price_lists.remove_item_button') }}
                        </button>
                    </td>
                `;
                itemsBody.appendChild(row);
                bindRowEvents(row);
            }

            addButton.addEventListener('click', addRow);

            itemsBody.querySelectorAll('tr').forEach((row) => bindRowEvents(row));

            itemsBody.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement) || !target.classList.contains('btn-remove-item')) {
                    return;
                }

                const row = target.closest('tr');
                if (!row) {
                    return;
                }

                if (rowsCount() <= 1) {
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
