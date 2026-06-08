@extends('layouts.base', ['title' => __('account.transfer_vehicle_types.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning mb-3" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.transfer_vehicle_types.heading')"
                            :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                            :instructions="__('account.transfer_vehicle_types.intro_instructions')"
                        />
                        <div>
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                @if ($importCatalogAvailable)
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#transferVehicleCatalogImportModal"
                                    >
                                        {{ __('account.transfer_vehicle_types.import_button') }}
                                    </button>
                                @endif
                                <a href="{{ route('account.transfer-vehicle-types.create') }}" class="btn btn-primary">
                                    {{ __('account.transfer_vehicle_types.create_button') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($vehicleTypes->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.transfer_vehicle_types.empty') }}
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-nowrap" style="width: 1%;">{{ __('account.transfer_vehicle_types.columns.order') }}</th>
                                            <th>{{ __('account.transfer_vehicle_types.columns.name') }}</th>
                                            <th>{{ __('account.transfer_vehicle_types.columns.code') }}</th>
                                            <th>{{ __('account.transfer_vehicle_types.columns.category') }}</th>
                                            <th>{{ __('account.transfer_vehicle_types.columns.max_passengers') }}</th>
                                            <th>{{ __('account.transfer_vehicle_types.columns.active') }}</th>
                                            <th class="text-end">{{ __('account.transfer_vehicle_types.columns.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vehicleTypes as $vt)
                                            <tr>
                                                <td class="text-center text-nowrap">
                                                    <form
                                                        method="POST"
                                                        action="{{ route('account.transfer-vehicle-types.move', [$vt, 'up']) }}"
                                                        class="d-inline-block"
                                                    >
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('account.transfer_vehicle_types.columns.order') }}">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-outline-secondary"
                                                                title="{{ __('account.transfer_vehicle_types.move_up') }}"
                                                                @disabled($loop->first)
                                                            >
                                                                <i data-feather="chevron-up" class="icon icon-xs" aria-hidden="true"></i>
                                                            </button>
                                                            <button
                                                                type="submit"
                                                                class="btn btn-outline-secondary"
                                                                title="{{ __('account.transfer_vehicle_types.move_down') }}"
                                                                formaction="{{ route('account.transfer-vehicle-types.move', [$vt, 'down']) }}"
                                                                @disabled($loop->last)
                                                            >
                                                                <i data-feather="chevron-down" class="icon icon-xs" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td class="fw-semibold">{{ $vt->name }}</td>
                                                <td class="text-muted small">{{ $vt->code ?? '—' }}</td>
                                                <td>{{ $vt->category?->name !== '' && $vt->category?->name !== null ? $vt->category->name : ($vt->category?->code ?? '—') }}</td>
                                                <td>{{ $vt->max_passengers ?? '—' }}</td>
                                                <td>
                                                    <span class="badge {{ $vt->active ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' }}">
                                                        {{ $vt->active ? __('account.transfer_vehicle_types.active_yes') : __('account.transfer_vehicle_types.active_no') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('account.transfer-vehicle-types.edit', $vt) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.transfer_vehicle_types.edit_button') }}
                                                        </a>
                                                        <form
                                                            method="POST"
                                                            action="{{ route('account.transfer-vehicle-types.destroy', $vt) }}"
                                                            onsubmit="return confirm('{{ __('account.transfer_vehicle_types.delete_confirm') }}')"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                {{ __('account.transfer_vehicle_types.delete_button') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($importCatalogAvailable)
        @include('account.transfer-vehicle-types.partials.import-catalog-modal', [
            'importCatalogCategoryOptions' => $importCatalogCategoryOptions,
            'importCatalogGrouped' => $importCatalogGrouped,
        ])
    @endif

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    @if ($importCatalogAvailable)
        <script>
            (function () {
                const modalEl = document.getElementById('transferVehicleCatalogImportModal');
                if (!modalEl) {
                    return;
                }

                const catFilters = () => Array.from(modalEl.querySelectorAll('.tvt-import-cat-filter'));
                const catBlocks = () => Array.from(modalEl.querySelectorAll('[data-tvt-import-category-block]'));
                const typeCheckboxes = () => Array.from(modalEl.querySelectorAll('.tvt-import-type-cb'));

                function syncBlocks() {
                    const checkedIds = new Set();
                    catFilters().forEach(function (cb) {
                        if (cb.checked) {
                            checkedIds.add(String(cb.value));
                        }
                    });
                    catBlocks().forEach(function (block) {
                        const id = String(block.getAttribute('data-tvt-import-category-block'));
                        const visible = checkedIds.has(id);
                        block.classList.toggle('d-none', !visible);
                        if (!visible) {
                            block.querySelectorAll('.tvt-import-type-cb').forEach(function (cb) {
                                cb.checked = false;
                            });
                        }
                    });
                    const noCats = checkedIds.size === 0;
                    const emptyMsg = modalEl.querySelector('.tvt-import-no-categories-msg');
                    const typesWrap = modalEl.querySelector('.tvt-import-types-wrap');
                    if (emptyMsg && typesWrap) {
                        emptyMsg.classList.toggle('d-none', !noCats);
                        typesWrap.classList.toggle('d-none', noCats);
                    }
                }

                catFilters().forEach(function (cb) {
                    cb.addEventListener('change', syncBlocks);
                });

                modalEl.addEventListener('click', function (e) {
                    const btn = e.target.closest('[data-tvt-import-action]');
                    if (!btn) {
                        return;
                    }
                    const action = btn.getAttribute('data-tvt-import-action');
                    if (action === 'select-all-categories') {
                        catFilters().forEach(function (cb) {
                            cb.checked = true;
                        });
                        syncBlocks();
                    } else if (action === 'clear-all-categories') {
                        catFilters().forEach(function (cb) {
                            cb.checked = false;
                        });
                        syncBlocks();
                    } else if (action === 'select-all-visible-types') {
                        typeCheckboxes().forEach(function (cb) {
                            const block = cb.closest('[data-tvt-import-category-block]');
                            if (block && !block.classList.contains('d-none')) {
                                cb.checked = true;
                            }
                        });
                    } else if (action === 'clear-all-types') {
                        typeCheckboxes().forEach(function (cb) {
                            cb.checked = false;
                        });
                    } else if (action === 'select-all-in-category') {
                        const catId = String(btn.getAttribute('data-tvt-import-category'));
                        const block = modalEl.querySelector('[data-tvt-import-category-block="' + catId + '"]');
                        if (block) {
                            block.querySelectorAll('.tvt-import-type-cb').forEach(function (cb) {
                                cb.checked = true;
                            });
                        }
                    } else if (action === 'clear-in-category') {
                        const catId = String(btn.getAttribute('data-tvt-import-category'));
                        const block = modalEl.querySelector('[data-tvt-import-category-block="' + catId + '"]');
                        if (block) {
                            block.querySelectorAll('.tvt-import-type-cb').forEach(function (cb) {
                                cb.checked = false;
                            });
                        }
                    }
                });

                syncBlocks();

                @if ($errors->isNotEmpty())
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
                @endif
            })();
        </script>
    @endif
@endsection
