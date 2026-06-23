@extends('layouts.base', ['title' => __('account.allocations.index_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.allocations.index_title')"
                            :instructions="__('account.allocations.index_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            @if ($operatorOptions !== [])
                                <a
                                    href="{{ route('account.allocations.index', array_filter([
                                        'operator' => $selectedOperatorId,
                                        'modal' => 'create',
                                    ])) }}"
                                    class="btn btn-primary"
                                >
                                    {{ __('account.allocations.create_button') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    @if ($operatorOptions === [])
                        <div class="alert alert-light border mb-0" role="alert">
                            {{ __('account.allocations.operators_empty') }}
                        </div>
                    @else
                        <form method="get" action="{{ route('account.allocations.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
                            <div>
                                <label for="allocation_operator_filter" class="form-label small mb-1">{{ __('account.allocations.filter_operator_label') }}</label>
                                <select name="operator" id="allocation_operator_filter" class="form-select form-select-sm" style="min-width: 16rem;" onchange="this.form.submit()">
                                    <option value="" @selected($selectedOperatorId === null)>{{ __('account.allocations.filter_operator_all') }}</option>
                                    @foreach ($operatorOptions as $operatorId => $operatorLabel)
                                        <option value="{{ $operatorId }}" @selected($selectedOperatorId === (int) $operatorId)>{{ $operatorLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($allocations->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                @if ($selectedOperatorId !== null)
                                    {{ __('account.allocations.empty') }}
                                @else
                                    {{ __('account.allocations.empty_all') }}
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            @if ($showOperatorColumn)
                                                <th>{{ __('account.allocations.columns.operator') }}</th>
                                            @endif
                                            <th>{{ __('account.allocations.columns.target') }}</th>
                                            <th>{{ __('account.allocations.columns.type') }}</th>
                                            <th>{{ __('account.allocations.columns.capacity') }}</th>
                                            <th>{{ __('account.allocations.columns.validity') }}</th>
                                            <th>{{ __('account.allocations.columns.active') }}</th>
                                            <th class="text-end">{{ __('account.allocations.columns.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allocations as $allocation)
                                            @php
                                                $rowOperator = $allocation->operatorAccount;
                                                $rowOperatorLabel = $rowOperator?->commercial_name
                                                    ?? $rowOperator?->name
                                                    ?? ('#' . $allocation->operator_id);
                                            @endphp
                                            <tr>
                                                @if ($showOperatorColumn)
                                                    <td class="fw-medium">{{ $rowOperatorLabel }}</td>
                                                @endif
                                                <td class="fw-medium">{{ $allocation->target_label }}</td>
                                                <td>{{ __('account.allocations.types.' . $allocation->allocation_type) }}</td>
                                                <td>
                                                    @if ($allocation->allocation_type === \App\Models\Allocation::TYPE_FREE_SALE)
                                                        <span class="text-muted">—</span>
                                                    @else
                                                        {{ number_format((int) $allocation->capacity) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($allocation->start_date || $allocation->end_date)
                                                        {{ locale_date_range(
                                                            $allocation->start_date,
                                                            $allocation->end_date,
                                                            null,
                                                            __('account.allocations.validity_open'),
                                                        ) }}
                                                    @else
                                                        <span class="text-muted">{{ __('account.allocations.validity_open') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($allocation->active)
                                                        <span class="badge bg-success-subtle text-success">{{ __('account.allocations.active_yes') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ __('account.allocations.active_no') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                                        <a
                                                            href="{{ route('account.allocations.index', ['operator' => $allocation->operator_id, 'modal' => 'edit', 'allocation' => $allocation->id]) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                        >
                                                            {{ __('account.allocations.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.allocations.destroy', $allocation) }}" class="d-inline" onsubmit="return confirm(@json(__('account.allocations.delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                {{ __('account.allocations.delete_button') }}
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

    @if ($formContext !== null)
        @php
            $formMode = $formContext['mode'] ?? 'create';
            $isEdit = $formMode === 'edit';
            $isOperatorPicker = $formMode === 'operator_picker';
            $variantOptions = $formContext['targetOptions']['variants'] ?? [];
            $hasTargets = ! $isOperatorPicker && count($variantOptions) > 0;
        @endphp
        <div
            class="modal fade"
            id="allocationFormModal"
            tabindex="-1"
            aria-labelledby="allocationFormModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allocationFormModalLabel">
                            {{ $isEdit ? __('account.allocations.edit_heading') : __('account.allocations.create_heading') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('account.allocations.modal_close') }}"></button>
                    </div>
                    <div class="modal-body">
                        @if ($isOperatorPicker)
                            <p class="text-muted small mb-3">{{ __('account.allocations.operator_picker_intro') }}</p>
                            <form method="get" action="{{ route('account.allocations.index') }}" id="allocation-operator-picker-form">
                                <input type="hidden" name="modal" value="create">
                                <label for="allocation_modal_operator" class="form-label">{{ __('account.allocations.filter_operator_label') }}</label>
                                <select name="operator" id="allocation_modal_operator" class="form-select" required>
                                    <option value="">{{ __('account.allocations.operator_picker_placeholder') }}</option>
                                    @foreach ($formContext['operatorOptions'] as $operatorId => $operatorLabel)
                                        <option value="{{ $operatorId }}">{{ $operatorLabel }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            @include('account.allocations.provider._form-fields', ['formContext' => $formContext])
                        @endif
                    </div>
                    @if ($isOperatorPicker)
                        <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                            <a
                                href="{{ route('account.allocations.index', array_filter(['operator' => $selectedOperatorId])) }}"
                                class="btn btn-light"
                            >
                                {{ __('account.allocations.cancel_button') }}
                            </a>
                            <button type="submit" form="allocation-operator-picker-form" class="btn btn-primary">
                                {{ __('account.allocations.operator_picker_continue') }}
                            </button>
                        </div>
                    @elseif ($hasTargets)
                        <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                            <a
                                href="{{ route('account.allocations.index', array_filter(['operator' => $selectedOperatorId])) }}"
                                class="btn btn-light"
                            >
                                {{ __('account.allocations.cancel_button') }}
                            </a>
                            <button type="submit" form="allocation-form" class="btn btn-primary">
                                {{ $isEdit ? __('account.allocations.update_button') : __('account.allocations.save_button') }}
                            </button>
                        </div>
                    @else
                        <div class="modal-footer">
                            <a
                                href="{{ route('account.allocations.index', array_filter(['operator' => $selectedOperatorId])) }}"
                                class="btn btn-light"
                            >
                                {{ __('account.allocations.cancel_button') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($hasTargets)
            <script>
                (function () {
                    const typeSelect = document.getElementById('allocation_type');
                    const capacityWrap = document.getElementById('capacity-field-wrap');
                    const capacityInput = document.getElementById('capacity');
                    if (!typeSelect || !capacityWrap || !capacityInput) {
                        return;
                    }

                    const freeSaleType = @json(\App\Models\Allocation::TYPE_FREE_SALE);

                    function refreshCapacityVisibility() {
                        const isFreeSale = typeSelect.value === freeSaleType;
                        capacityWrap.style.display = isFreeSale ? 'none' : '';
                        capacityInput.required = !isFreeSale;
                        if (isFreeSale) {
                            capacityInput.value = '';
                        }
                    }

                    typeSelect.addEventListener('change', refreshCapacityVisibility);
                    refreshCapacityVisibility();
                })();
            </script>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modalEl = document.getElementById('allocationFormModal');
                if (!modalEl || typeof bootstrap === 'undefined') {
                    return;
                }

                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                modalEl.addEventListener('hidden.bs.modal', function () {
                    const baseUrl = @json(route('account.allocations.index', array_filter(['operator' => $selectedOperatorId])));
                    if (window.location.href !== baseUrl) {
                        window.location.replace(baseUrl);
                    }
                });
            });
        </script>
    @endif

    <x-site-footer-simple />
@endsection
