@php
    $isEdit = $package !== null;
    $providerOptions = $providerOptions ?? [];
    $offersByProvider = $offersByProvider ?? [];
    $itemsOld = old('items');
    $items = is_array($itemsOld)
        ? $itemsOld
        : ($isEdit
            ? $package->items->sortBy('sort_order')->values()->map(fn ($item) => [
                'id' => (string) $item->id,
                'provider_id' => (string) ($item->serviceOffer?->provider_id ?? ''),
                'service_offer_id' => (string) $item->service_offer_id,
                'service_variant_id' => (string) $item->service_variant_id,
                'day_number' => $item->day_number,
                'quantity' => $item->quantity,
                'inclusion_mode' => $item->inclusion_mode,
                'notes' => $item->notes,
            ])->toArray()
            : [[
                'provider_id' => '',
                'service_offer_id' => '',
                'service_variant_id' => '',
                'day_number' => '',
                'quantity' => 1,
                'inclusion_mode' => 'included',
                'notes' => '',
            ]]);
    $packageConditionsOld = old('package_conditions');
    $packageConditions = is_array($packageConditionsOld)
        ? $packageConditionsOld
        : ($packageConditions ?? []);
    $editItemIds = $editItemIds ?? [];
    $activeTab = 'header';
    if ($errors->any()) {
        foreach (array_keys($errors->getMessages()) as $errorKey) {
            if ($errorKey === 'package_conditions' || str_starts_with((string) $errorKey, 'package_conditions.')) {
                $activeTab = 'conditions';
                break;
            }
            if (str_contains((string) $errorKey, 'condition_overrides')) {
                $activeTab = 'conditions';
                break;
            }
            if ($errorKey === 'items' || str_starts_with((string) $errorKey, 'items.')) {
                $activeTab = 'items';
            }
        }
    }
    $statusValue = old('status', $package->status ?? 'active');
    $actionLabels = [
        '' => __('account.operator_packages.conditions.action_inherit'),
        'append_top' => __('account.operator_packages.conditions.action_append_top'),
        'append_bottom' => __('account.operator_packages.conditions.action_append_bottom'),
        'replace' => __('account.operator_packages.conditions.action_replace'),
        'suppress' => __('account.operator_packages.conditions.action_suppress'),
    ];
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.operator_packages.edit_page_title') : __('account.operator_packages.create_page_title')])


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
                        :title="$isEdit ? __('account.operator_packages.edit_title') : __('account.operator_packages.create_heading')"
                        :subtitle="$isEdit ? ($package->name ?? $package->title ?? ('#' . $package->id)) : ($account->commercial_name ?? $account->name ?? $account->nick)"
                        :instructions="__('account.operator_packages.form_instructions')"
                    />
                </div>
            </div>

            @if ($providerOptions === [])
                <div class="alert alert-warning mt-3" role="alert">
                    {{ __('account.operator_packages.no_providers') }}
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ $submitRoute }}" id="operator-package-form">
                                @csrf
                                @if ($submitMethod !== 'POST')
                                    @method($submitMethod)
                                @endif

                                <ul class="nav nav-tabs flex-wrap mb-3" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if ($activeTab === 'header') active @endif" data-bs-toggle="tab" href="#package-tab-header" role="tab">
                                            {{ __('account.operator_packages.tab_header') }}
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if ($activeTab === 'items') active @endif" data-bs-toggle="tab" href="#package-tab-items" role="tab">
                                            {{ __('account.operator_packages.tab_items') }}
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if ($activeTab === 'conditions') active @endif" data-bs-toggle="tab" href="#package-tab-conditions" role="tab">
                                            {{ __('account.operator_packages.tab_conditions') }}
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade @if ($activeTab === 'header') show active @endif" id="package-tab-header" role="tabpanel">
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <fieldset class="mb-0">
                                                    <legend class="form-label col-form-label pt-0">{{ __('account.operator_packages.fields.status') }}</legend>
                                                    <div class="d-flex flex-wrap gap-2" role="group" aria-label="{{ __('account.operator_packages.fields.status') }}">
                                                        @foreach (['active', 'hidden', 'paused', 'archived'] as $statusOption)
                                                            <input
                                                                type="radio"
                                                                class="btn-check @error('status') is-invalid @enderror"
                                                                name="status"
                                                                id="package-status-{{ $statusOption }}"
                                                                value="{{ $statusOption }}"
                                                                @checked($statusValue === $statusOption)
                                                                @if ($loop->first) required @endif
                                                            >
                                                            <label class="btn btn-sm btn-outline-secondary" for="package-status-{{ $statusOption }}">
                                                                {{ __('account.operator_packages.status.'.$statusOption) }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </fieldset>
                                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <div class="d-flex flex-column flex-sm-row flex-wrap gap-3 gap-sm-4">
                                                    <div class="form-check mb-0">
                                                        <input type="hidden" name="is_featured" value="0">
                                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $package->is_featured ?? false))>
                                                        <label class="form-check-label" for="is_featured">{{ __('account.operator_packages.fields.is_featured') }}</label>
                                                    </div>
                                                    <div class="form-check mb-0">
                                                        <input type="hidden" name="is_public" value="0">
                                                        <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" @checked(old('is_public', $package->is_public ?? false))>
                                                        <label class="form-check-label" for="is_public">{{ __('account.operator_packages.fields.is_public') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                                        @foreach ($languages as $language)
                                            @php
                                                $langId = (int) $language->id;
                                                $translation = $isEdit
                                                    ? $package->translations->firstWhere('language_id', $langId)
                                                    : null;
                                            @endphp
                                            <div class="col">
                                            <div class="border rounded p-3 h-100">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h5 class="h6 mb-0">{{ $language->display_name }}</h5>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary translate-from-language-btn"
                                                        data-source-language-id="{{ $langId }}"
                                                        title="{{ __('account.operator_packages.translate_from_language', ['language' => $language->display_name]) }}"
                                                    >
                                                        <span aria-hidden="true">🌐</span>
                                                    </button>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label required-label" for="translations-{{ $langId }}-name">{{ __('account.operator_packages.fields.name') }}</label>
                                                    <input
                                                        type="text"
                                                        id="translations-{{ $langId }}-name"
                                                        name="translations[{{ $langId }}][name]"
                                                        data-language-id="{{ $langId }}"
                                                        class="form-control @error("translations.{$langId}.name") is-invalid @enderror"
                                                        maxlength="255"
                                                        required
                                                        value="{{ old("translations.{$langId}.name", $translation?->name ?? '') }}"
                                                    >
                                                    @error("translations.{$langId}.name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label" for="translations-{{ $langId }}-description">{{ __('account.operator_packages.fields.description') }}</label>
                                                    <textarea
                                                        id="translations-{{ $langId }}-description"
                                                        name="translations[{{ $langId }}][description]"
                                                        data-language-id="{{ $langId }}"
                                                        class="form-control @error("translations.{$langId}.description") is-invalid @enderror"
                                                        rows="4"
                                                    >{{ old("translations.{$langId}.description", $translation?->description ?? '') }}</textarea>
                                                    @error("translations.{$langId}.description")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>

                                    <div class="tab-pane fade @if ($activeTab === 'items') show active @endif" id="package-tab-items" role="tabpanel">
                                        <p class="text-muted small">{{ __('account.operator_packages.items_help') }}</p>
                                        <div class="table-responsive">
                                            <table class="table align-middle" id="package-items-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('account.operator_packages.fields.provider') }}</th>
                                                        <th>{{ __('account.operator_packages.fields.offer') }}</th>
                                                        <th>{{ __('account.operator_packages.fields.day_number') }}</th>
                                                        <th class="text-center text-nowrap" style="width: 1%;">{{ __('account.operator_packages.fields.sort_order') }}</th>
                                                        <th>{{ __('account.operator_packages.fields.quantity') }}</th>
                                                        <th>{{ __('account.operator_packages.fields.inclusion_mode') }}</th>
                                                        <th>{{ __('account.operator_packages.fields.notes') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="package-items-body">
                                                    @foreach ($items as $index => $item)
                                                        @include('account.operator-packages.partials.item-row', [
                                                            'index' => $index,
                                                            'item' => $item,
                                                            'providerOptions' => $providerOptions,
                                                            'offersByProvider' => $offersByProvider,
                                                        ])
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="package-add-item">
                                            {{ __('account.operator_packages.add_item_button') }}
                                        </button>
                                    </div>

                                    @include('account.operator-packages.partials.conditions-tab', [
                                        'activeTab' => $activeTab,
                                        'packageConditions' => $packageConditions,
                                        'packageTopicOptions' => $packageTopicOptions ?? [],
                                        'languages' => $languages,
                                        'itemConditionsUrl' => $itemConditionsUrl ?? '',
                                    ])
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    @if ($isEdit)
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="Livewire.dispatch('open-operator-package-preview', { packageUuid: @js($package->uuid) })"
                                        >
                                            {{ __('account.operator_packages.preview_button') }}
                                        </button>
                                    @endif
                                    <button type="submit" class="btn btn-primary" @disabled($providerOptions === [])>
                                        {{ $isEdit ? __('account.operator_packages.update_button') : __('account.operator_packages.save_button') }}
                                    </button>
                                    <a href="{{ $cancelRoute }}" class="btn btn-outline-secondary">{{ __('account.operator_packages.cancel_button') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <template id="package-item-row-template">
        @include('account.operator-packages.partials.item-row', [
            'index' => '__INDEX__',
            'item' => [
                'provider_id' => '',
                'service_offer_id' => '',
                'service_variant_id' => '',
                'day_number' => '',
                'quantity' => 1,
                'inclusion_mode' => 'included',
                'notes' => '',
            ],
            'providerOptions' => $providerOptions,
            'offersByProvider' => $offersByProvider,
        ])
    </template>

    @if ($isEdit)
        <livewire:account.operator-package-preview-modal />
    @endif

@endsection

@section('script')
    <script>
        (function () {
            const offersByProvider = @json($offersByProvider);
            const providerPlaceholder = @json(__('account.operator_packages.fields.provider_placeholder'));
            const offerPlaceholder = @json(__('account.operator_packages.fields.offer_placeholder'));
            let nextIndex = document.querySelectorAll('#package-items-body tr.package-item-row').length;

            function buildOfferOptions(providerId, selectedOfferId) {
                const offers = offersByProvider[providerId] || offersByProvider[String(providerId)] || [];
                let html = `<option value="">${offerPlaceholder}</option>`;
                for (const offer of offers) {
                    const selected = String(selectedOfferId) === String(offer.offer_id) ? ' selected' : '';
                    html += `<option value="${offer.offer_id}" data-service-id="${offer.service_id}" data-variant-id="${offer.service_variant_id ?? ''}"${selected}>${offer.label}</option>`;
                }
                return html;
            }

            function bindRow(row) {
                const providerSelect = row.querySelector('.package-item-provider');
                const offerSelect = row.querySelector('.package-item-offer');
                const variantIdInput = row.querySelector('.package-item-variant-id');

                function syncOfferOptions() {
                    const providerId = providerSelect.value;
                    const currentOffer = offerSelect.value;
                    offerSelect.innerHTML = buildOfferOptions(providerId, currentOffer);
                    syncHiddenIds();
                }

                function syncHiddenIds() {
                    const opt = offerSelect.selectedOptions[0];
                    if (!opt || !opt.value) {
                        variantIdInput.value = '';
                        return;
                    }
                    variantIdInput.value = opt.dataset.variantId || '';
                }

                providerSelect.addEventListener('change', () => {
                    offerSelect.innerHTML = `<option value="">${offerPlaceholder}</option>`;
                    syncOfferOptions();
                });
                offerSelect.addEventListener('change', syncHiddenIds);

                syncOfferOptions();
            }

            function reindexPackageRows() {
                const rows = document.querySelectorAll('#package-items-body tr.package-item-row');
                rows.forEach((row, index) => {
                    row.querySelectorAll('[name^="items["]').forEach((input) => {
                        input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                    });

                    const upBtn = row.querySelector('.package-item-move-up');
                    const downBtn = row.querySelector('.package-item-move-down');
                    if (upBtn) upBtn.disabled = index === 0;
                    if (downBtn) downBtn.disabled = index === rows.length - 1;
                });
                nextIndex = rows.length;
                if (typeof window.packageConditionsReindexItems === 'function') {
                    window.packageConditionsReindexItems();
                }
            }

            function movePackageRow(row, direction) {
                const sibling = direction < 0 ? row.previousElementSibling : row.nextElementSibling;
                if (!sibling?.classList.contains('package-item-row')) {
                    return;
                }

                if (direction < 0) {
                    row.parentNode.insertBefore(row, sibling);
                } else {
                    row.parentNode.insertBefore(sibling, row);
                }

                reindexPackageRows();
            }

            document.querySelectorAll('#package-items-body tr.package-item-row').forEach(bindRow);
            reindexPackageRows();

            document.getElementById('package-add-item')?.addEventListener('click', () => {
                const template = document.getElementById('package-item-row-template');
                if (!template) return;
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
                const tbody = document.getElementById('package-items-body');
                tbody.insertAdjacentHTML('beforeend', html);
                const row = tbody.lastElementChild;
                if (row) bindRow(row);
                reindexPackageRows();
            });

            document.getElementById('package-items-body')?.addEventListener('click', (event) => {
                const moveUpBtn = event.target.closest('.package-item-move-up');
                if (moveUpBtn) {
                    const row = moveUpBtn.closest('tr.package-item-row');
                    if (row) movePackageRow(row, -1);
                    return;
                }

                const moveDownBtn = event.target.closest('.package-item-move-down');
                if (moveDownBtn) {
                    const row = moveDownBtn.closest('tr.package-item-row');
                    if (row) movePackageRow(row, 1);
                    return;
                }

                const btn = event.target.closest('.package-item-remove');
                if (!btn) return;
                const row = btn.closest('tr.package-item-row');
                const rows = document.querySelectorAll('#package-items-body tr.package-item-row');
                if (rows.length <= 1) return;
                row?.remove();
                reindexPackageRows();
            });
        })();
    </script>
    @include('account.operator-packages.partials.conditions-script', [
        'itemConditionsUrl' => $itemConditionsUrl ?? route('account.operator-packages.item-conditions'),
        'languagesForConditions' => $languages->map(fn ($language) => [
            'id' => (int) $language->id,
            'display_name' => $language->display_name,
        ])->values(),
        'actionLabels' => $actionLabels,
        'editItemIds' => $editItemIds,
    ])
    @include('partials.translation-from-language-script', [
        'translateRoute' => route('account.operator-packages.translate-translations'),
        'formSelector' => '#operator-package-form',
    ])
@endsection
