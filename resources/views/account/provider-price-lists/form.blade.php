@php
    $isEdit = $priceList !== null;
    $requiresNotificationTab = $isEdit && ($requiresNotificationTab ?? false);
    $activeOperatorLabels = $activeOperatorLabels ?? [];
    $itemsOld = old('items');
    $items = is_array($itemsOld)
        ? $itemsOld
        : ($isEdit ? $priceList->items->map(fn ($item) => [
            'service_variant_id' => $item->service_variant_id,
            'pricing_mode' => $item->pricing_mode,
            'price' => $item->price,
        ])->toArray() : [[
            'service_variant_id' => '',
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
            if (
                $requiresNotificationTab
                && (
                    $errorKey === 'notification_choice'
                    || str_starts_with($errorKey, 'notification_')
                )
            ) {
                $activePriceListTab = 'notification';
                break;
            }
        }
    }
    $notificationChoice = old('notification_choice');
    $showNotificationOptions = $notificationChoice === 'notify';
    $pricingModeHelpHtml = \App\Support\ServiceWizardVariantCatalogHelpers::pricingModeHelpHtml(
        accountTypeId: \App\Support\CurrentCatalogHelperAccountContext::primaryAccountTypeId(),
    );
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.price_lists.edit_page_title') : __('account.price_lists.create_page_title')])

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
                        :title="$isEdit ? __('account.price_lists.edit_title') : __('account.price_lists.create_heading')"
                        :subtitle="$isEdit ? $priceList->name : ($account->commercial_name ?? $account->name ?? $account->nick)"
                        :instructions="__('account.price_lists.form_instructions')"
                    >
                        @if ($isEdit)
                            <p class="mt-2 mb-0 small">
                                <a href="{{ route('account.provider-price-lists.assignments.edit', $priceList) }}">{{ __('account.price_lists.assignments_link_from_edit') }}</a>
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
                                    @if ($requiresNotificationTab)
                                        <li class="nav-item" role="presentation">
                                            <a
                                                class="nav-link @if ($activePriceListTab === 'notification') active @endif"
                                                id="price-list-tab-notification-link"
                                                data-bs-toggle="tab"
                                                href="#price-list-tab-notification"
                                                role="tab"
                                                aria-controls="price-list-tab-notification"
                                                aria-selected="{{ $activePriceListTab === 'notification' ? 'true' : 'false' }}"
                                            >{{ __('account.price_lists.tab_notification') }}</a>
                                        </li>
                                    @endif
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
                                                    <x-locale-date-input
                                                        id="valid_from"
                                                        name="valid_from"
                                                        :value="old('valid_from', $priceList?->valid_from)"
                                                        class="{{ $errors->has('valid_from') ? 'is-invalid' : '' }}"
                                                    />
                                                    @error('valid_from')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="valid_to" class="form-label">{{ __('account.price_lists.fields.valid_to') }}</label>
                                                    <x-locale-date-input
                                                        id="valid_to"
                                                        name="valid_to"
                                                        :value="old('valid_to', $priceList?->valid_to)"
                                                        class="{{ $errors->has('valid_to') ? 'is-invalid' : '' }}"
                                                    />
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

                                        <div class="table-responsive">
                                            <table class="table align-middle" id="items-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('account.price_lists.fields.variant_specific') }}</th>
                                                        <th>
                                                            <span class="d-inline-flex align-items-center gap-1">
                                                                {{ __('account.price_lists.fields.pricing_mode') }}
                                                                <x-catalog-helper-icon
                                                                    :html="$pricingModeHelpHtml"
                                                                    trigger-id="price-list-pricing-mode-helper"
                                                                    content-id="price-list-pricing-mode-helper-html"
                                                                    :aria-label="__('account.price_lists.pricing_mode_helper_aria')"
                                                                />
                                                            </span>
                                                        </th>
                                                        <th>{{ __('account.price_lists.fields.price') }}</th>
                                                        <th class="text-end">{{ __('account.price_lists.fields.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="items-body">
                                                    @foreach($items as $index => $item)
                                                        <tr>
                                                            <td>
                                                                <select name="items[{{ $index }}][service_variant_id]" class="form-select" data-role="item-variant" required>
                                                                    <option value="">{{ __('account.price_lists.fields.variant_placeholder') }}</option>
                                                                    @foreach($serviceVariantOptions as $variantId => $variantLabel)
                                                                        <option value="{{ $variantId }}" @selected((string) ($item['service_variant_id'] ?? '') === (string) $variantId)>
                                                                            {{ $variantLabel }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="items[{{ $index }}][pricing_mode]" class="form-select" data-role="pricing-mode-select">
                                                                    <option value="" @selected(($item['pricing_mode'] ?? null) === null || ($item['pricing_mode'] ?? '') === '')>{{ __('account.price_lists.pricing_mode.variant_base') }}</option>
                                                                    <option value="fixed" @selected(($item['pricing_mode'] ?? '') === 'fixed')>{{ __('account.price_lists.pricing_mode.fixed') }}</option>
                                                                    <option value="percentage" @selected(($item['pricing_mode'] ?? '') === 'percentage')>{{ __('account.price_lists.pricing_mode.percentage') }}</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div
                                                                    data-role="item-price-variant-base"
                                                                    class="form-control bg-light text-muted border-0 px-0 d-none"
                                                                    aria-hidden="true"
                                                                ></div>
                                                                <input
                                                                    type="number"
                                                                    name="items[{{ $index }}][price]"
                                                                    class="form-control"
                                                                    data-role="item-price"
                                                                    step="{{ $priceStep }}"
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

                                    @if ($requiresNotificationTab)
                                        <div
                                            class="tab-pane fade @if ($activePriceListTab === 'notification') show active @endif"
                                            id="price-list-tab-notification"
                                            role="tabpanel"
                                            aria-labelledby="price-list-tab-notification-link"
                                        >
                                            <div class="alert alert-warning" role="alert">
                                                <p class="mb-2">{{ __('account.price_lists.notification_tab.alert') }}</p>
                                                @if ($activeOperatorLabels !== [])
                                                    <div class="price-list-notification-operators small mb-0">
                                                        <strong>{{ __('account.price_lists.notification_tab.operators_heading') }}</strong>
                                                        <ul class="mb-0 ps-3 mt-1" style="max-height: 6.5rem; overflow-y: auto;">
                                                            @foreach ($activeOperatorLabels as $operatorLabel)
                                                                <li>{{ $operatorLabel }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>

                                            <fieldset class="mb-3">
                                                <legend class="form-label fs-6 mb-2 required-label">{{ __('account.price_lists.notification_tab.choice_label') }}</legend>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('notification_choice') is-invalid @enderror"
                                                        type="radio"
                                                        name="notification_choice"
                                                        id="notification_choice_notify"
                                                        value="notify"
                                                        @checked($notificationChoice === 'notify')
                                                    >
                                                    <label class="form-check-label" for="notification_choice_notify">
                                                        {{ __('account.price_lists.notification_tab.choice_notify') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('notification_choice') is-invalid @enderror"
                                                        type="radio"
                                                        name="notification_choice"
                                                        id="notification_choice_dont_notify"
                                                        value="dont_notify"
                                                        @checked($notificationChoice === 'dont_notify')
                                                    >
                                                    <label class="form-check-label" for="notification_choice_dont_notify">
                                                        {{ __('account.price_lists.notification_tab.choice_dont_notify') }}
                                                    </label>
                                                </div>
                                                @error('notification_choice')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </fieldset>

                                            <div
                                                id="notification-notify-options"
                                                class="@unless($showNotificationOptions) d-none @endunless"
                                            >
                                                <div class="mb-3">
                                                    <label for="notification_message" class="form-label">
                                                        {{ __('account.price_lists.notification_tab.message_label') }}
                                                    </label>
                                                    <textarea
                                                        id="notification_message"
                                                        name="notification_message"
                                                        class="form-control @error('notification_message') is-invalid @enderror"
                                                        rows="4"
                                                        maxlength="4000"
                                                        placeholder="{{ __('account.price_lists.notification_tab.message_placeholder') }}"
                                                    >{{ old('notification_message') }}</textarea>
                                                    <div class="form-text">{{ __('account.price_lists.notification_tab.message_help') }}</div>
                                                    @error('notification_message')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-check mb-2">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        id="notification_send_email"
                                                        name="notification_send_email"
                                                        value="1"
                                                        @checked((bool) old('notification_send_email'))
                                                    >
                                                    <label class="form-check-label" for="notification_send_email">
                                                        {{ __('account.price_lists.notification_tab.send_email') }}
                                                    </label>
                                                </div>

                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        id="notification_cc_me"
                                                        name="notification_cc_me"
                                                        value="1"
                                                        @checked((bool) old('notification_cc_me'))
                                                    >
                                                    <label class="form-check-label" for="notification_cc_me">
                                                        {{ __('account.price_lists.notification_tab.cc_me') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
            const labelVariantBase = @json(__('account.price_lists.pricing_mode.variant_base'));
            const labelFixed = @json(__('account.price_lists.pricing_mode.fixed'));
            const labelPercentage = @json(__('account.price_lists.pricing_mode.percentage'));
            const variantBasePriceDisplay = @json(__('account.price_lists.variant_base_price_display'));
            const variantBasePriceUnavailable = @json(__('account.price_lists.variant_base_price_unavailable'));
            const variantBasePrices = @json($variantBasePrices ?? []);
            const currencyCodesById = @json($currencyCodesById ?? []);
            const priceFormatSettings = @json($priceFormatSettings);
            const priceStep = @json($priceStep);
            const modePercentage = 'percentage';
            const modeVariantBase = '';
            const currencySelect = document.getElementById('currency_id');

            function selectedListCurrencyCode() {
                if (!(currencySelect instanceof HTMLSelectElement) || currencySelect.value === '') {
                    return '';
                }

                return currencyCodesById[currencySelect.value] ?? currencyCodesById[Number(currencySelect.value)] ?? '';
            }

            function formatPriceAmount(amount) {
                const decimals = priceFormatSettings.decimals ?? 2;
                const decSep = priceFormatSettings.decimal_separator ?? '.';
                const thouSep = priceFormatSettings.thousands_separator ?? ',';

                const n = Number(amount);
                if (!Number.isFinite(n)) {
                    return '—';
                }

                const fixed = n.toFixed(decimals);
                const parts = fixed.split('.');
                let intPart = parts[0];
                const fracPart = parts[1] ?? '';

                intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, thouSep);

                return decimals > 0 ? intPart + decSep + fracPart : intPart;
            }

            function variantBasePriceLabel(variantId) {
                const code = selectedListCurrencyCode();
                if (variantId === '' || variantId === null || variantId === undefined) {
                    return variantBasePriceUnavailable.replace(':code', code || '—');
                }

                const data = variantBasePrices[variantId] ?? variantBasePrices[String(variantId)] ?? variantBasePrices[Number(variantId)];
                if (!data || data.amount === null || data.amount === undefined) {
                    return variantBasePriceUnavailable.replace(':code', code || '—');
                }

                return variantBasePriceDisplay
                    .replace(':code', code || '—')
                    .replace(':amount', formatPriceAmount(data.amount));
            }

            function applyPricingBehavior(row) {
                const pricingModeSelect = row.querySelector('[data-role="pricing-mode-select"]');
                const variantSelect = row.querySelector('[data-role="item-variant"]');
                const priceInput = row.querySelector('[data-role="item-price"]');
                const priceDisplay = row.querySelector('[data-role="item-price-variant-base"]');
                const priceHelp = row.querySelector('[data-role="item-price-help"]');

                if (!pricingModeSelect || !priceInput || !priceHelp) {
                    return;
                }

                const mode = pricingModeSelect.value;
                const isPercentage = mode === modePercentage;
                const isVariantBase = mode === modeVariantBase;

                if (isVariantBase) {
                    priceInput.value = '';
                    priceInput.classList.add('d-none');
                    priceInput.removeAttribute('required');
                    priceHelp.textContent = '';
                    priceHelp.classList.add('d-none');

                    if (priceDisplay instanceof HTMLElement) {
                        const variantId = variantSelect instanceof HTMLSelectElement ? variantSelect.value : '';
                        priceDisplay.textContent = variantBasePriceLabel(variantId);
                        priceDisplay.classList.remove('d-none');
                        priceDisplay.removeAttribute('aria-hidden');
                    }
                } else {
                    priceInput.classList.remove('d-none');
                    priceInput.setAttribute('required', 'required');
                    priceInput.placeholder = isPercentage ? percentagePlaceholder : amountPlaceholder;
                    priceHelp.textContent = isPercentage ? hintPercentageBase : hintFixedList;
                    priceHelp.classList.remove('d-none');

                    if (priceDisplay instanceof HTMLElement) {
                        priceDisplay.textContent = '';
                        priceDisplay.classList.add('d-none');
                        priceDisplay.setAttribute('aria-hidden', 'true');
                    }
                }
            }

            function bindRowEvents(row) {
                const pricingModeSelect = row.querySelector('[data-role="pricing-mode-select"]');
                const variantSelect = row.querySelector('[data-role="item-variant"]');

                if (pricingModeSelect) {
                    pricingModeSelect.addEventListener('change', () => applyPricingBehavior(row));
                }

                if (variantSelect) {
                    variantSelect.addEventListener('change', () => applyPricingBehavior(row));
                }

                applyPricingBehavior(row);
            }

            function addRow() {
                const index = rowsCount();
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <select name="items[${index}][service_variant_id]" class="form-select" data-role="item-variant" required>
                            <option value="">${variantPlaceholder}</option>
                            ${variantOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <select name="items[${index}][pricing_mode]" class="form-select" data-role="pricing-mode-select">
                            <option value="">${labelVariantBase}</option>
                            <option value="fixed">${labelFixed}</option>
                            <option value="percentage">${labelPercentage}</option>
                        </select>
                    </td>
                    <td>
                        <div data-role="item-price-variant-base" class="form-control bg-light text-muted border-0 px-0 d-none" aria-hidden="true"></div>
                        <input type="number" name="items[${index}][price]" class="form-control" data-role="item-price" step="${priceStep}">
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

            if (currencySelect) {
                currencySelect.addEventListener('change', () => {
                    itemsBody.querySelectorAll('tr').forEach((row) => applyPricingBehavior(row));
                });
            }

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

            const notificationNotifyOptions = document.getElementById('notification-notify-options');
            const notificationRadios = document.querySelectorAll('input[name="notification_choice"]');
            if (notificationNotifyOptions && notificationRadios.length > 0) {
                function syncNotificationOptionsVisibility() {
                    const selected = document.querySelector('input[name="notification_choice"]:checked');
                    const showOptions = selected instanceof HTMLInputElement && selected.value === 'notify';
                    notificationNotifyOptions.classList.toggle('d-none', !showOptions);
                }

                notificationRadios.forEach((radio) => {
                    radio.addEventListener('change', syncNotificationOptionsVisibility);
                });

                syncNotificationOptionsVisibility();
            }
        })();
    </script>

    @include('partials.catalog-helper-popover-script')

    <x-site-footer-simple />

@endsection
