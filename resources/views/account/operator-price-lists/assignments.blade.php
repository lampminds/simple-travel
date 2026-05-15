@php
    $agencyOptions = $agencyOptions ?? [];
    $hasAgencies = count($agencyOptions) > 0;
    $defaultAssignmentRow = [
        'agency_account_id' => '',
        'adjustment_type' => 'none',
        'adjustment_value' => '',
        'valid_from' => '',
        'valid_to' => '',
        'is_active' => true,
    ];
    $assignmentsOld = old('assignments');
    if (is_array($assignmentsOld)) {
        $assignments = $assignmentsOld;
    } else {
        $assignments = $priceList->assignments
            ->map(fn ($a) => [
                'agency_account_id' => $a->agency_id,
                'adjustment_type' => $a->adjustment_type,
                'adjustment_value' => $a->adjustment_value !== null ? (string) $a->adjustment_value : '',
                'valid_from' => $a->valid_from ? $a->valid_from->format('Y-m-d') : '',
                'valid_to' => $a->valid_to ? $a->valid_to->format('Y-m-d') : '',
                'is_active' => $a->is_active,
            ])
            ->values()
            ->toArray();
    }
    if ($hasAgencies && $assignments === []) {
        $assignments = [$defaultAssignmentRow];
    }
    $priceDecimals = max(0, min(3, (int) ($priceFormatSettings['decimals'] ?? 2)));
    $priceStep = $priceDecimals === 0 ? '1' : '0.'.str_repeat('0', $priceDecimals - 1).'1';
    $thousandsSeparator = (string) ($priceFormatSettings['thousands_separator'] ?? ',');
    $decimalSeparator = (string) ($priceFormatSettings['decimal_separator'] ?? '.');
@endphp

@extends('layouts.base', ['title' => __('account.operator_price_lists.assignments_page_title', ['name' => $priceList->name])])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

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
                        <h3 class="my-0">{{ __('account.operator_price_lists.assignments_heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ __('account.operator_price_lists.assignments_intro', ['name' => $priceList->name]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($hasAgencies)
                                <form method="POST" action="{{ route('account.operator-price-lists.assignments.update', $priceList) }}">
                                    @csrf
                                    @method('PUT')

                                    <p class="small text-muted mb-3">{{ __('account.operator_price_lists.assignments_help') }}</p>
                                    <p class="small text-muted mb-3">
                                        {{ __('account.operator_price_lists.fields.adjustment_value') }}: 12{{ $thousandsSeparator }}345{{ $decimalSeparator }}{{ str_repeat('0', max(1, $priceDecimals)) }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="mb-0">{{ __('account.operator_price_lists.assignments_rows_title') }}</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-assignment">
                                            {{ __('account.operator_price_lists.add_assignment_button') }}
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table align-middle" id="assignments-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('account.operator_price_lists.fields.agency') }}</th>
                                                    <th>{{ __('account.operator_price_lists.fields.adjustment_type') }}</th>
                                                    <th>{{ __('account.operator_price_lists.fields.adjustment_value') }}</th>
                                                    <th>{{ __('account.operator_price_lists.fields.valid_from') }}</th>
                                                    <th>{{ __('account.operator_price_lists.fields.valid_to') }}</th>
                                                    <th>{{ __('account.operator_price_lists.fields.assignment_active') }}</th>
                                                    <th class="text-end">{{ __('account.operator_price_lists.fields.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="assignments-body">
                                                @foreach ($assignments as $aIndex => $arow)
                                                    <tr>
                                                        <td>
                                                            <select name="assignments[{{ $aIndex }}][agency_account_id]" class="form-select">
                                                                <option value="">{{ __('account.operator_price_lists.fields.agency_placeholder') }}</option>
                                                                @foreach ($agencyOptions as $agencyId => $agencyLabel)
                                                                    <option value="{{ $agencyId }}" @selected((string) ($arow['agency_account_id'] ?? '') === (string) $agencyId)>
                                                                        {{ $agencyLabel }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('assignments.'.$aIndex.'.agency_account_id')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <select name="assignments[{{ $aIndex }}][adjustment_type]" class="form-select">
                                                                <option value="none" @selected(($arow['adjustment_type'] ?? 'none') === 'none')>{{ __('account.operator_price_lists.assignment_adjustment.none') }}</option>
                                                                <option value="percentage" @selected(($arow['adjustment_type'] ?? '') === 'percentage')>{{ __('account.operator_price_lists.assignment_adjustment.percentage') }}</option>
                                                                <option value="fixed" @selected(($arow['adjustment_type'] ?? '') === 'fixed')>{{ __('account.operator_price_lists.assignment_adjustment.fixed') }}</option>
                                                            </select>
                                                            @error('assignments.'.$aIndex.'.adjustment_type')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input
                                                                type="number"
                                                                step="{{ $priceStep }}"
                                                                name="assignments[{{ $aIndex }}][adjustment_value]"
                                                                class="form-control"
                                                                value="{{ $arow['adjustment_value'] ?? '' }}"
                                                                placeholder="{{ __('account.operator_price_lists.fields.adjustment_value_placeholder') }}"
                                                            >
                                                            @error('assignments.'.$aIndex.'.adjustment_value')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="date" name="assignments[{{ $aIndex }}][valid_from]" class="form-control" value="{{ $arow['valid_from'] ?? '' }}">
                                                            @error('assignments.'.$aIndex.'.valid_from')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="date" name="assignments[{{ $aIndex }}][valid_to]" class="form-control" value="{{ $arow['valid_to'] ?? '' }}">
                                                            @error('assignments.'.$aIndex.'.valid_to')
                                                                <div class="text-danger small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input type="hidden" name="assignments[{{ $aIndex }}][is_active]" value="0">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    name="assignments[{{ $aIndex }}][is_active]"
                                                                    value="1"
                                                                    @checked(filter_var($arow['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN))
                                                                >
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-assignment">
                                                                {{ __('account.operator_price_lists.remove_assignment_button') }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-4">
                                        <a class="btn btn-light" href="{{ route('account.operator-price-lists.index') }}">{{ __('account.operator_price_lists.assignments_cancel') }}</a>
                                        <a class="btn btn-outline-secondary" href="{{ route('account.operator-price-lists.edit', $priceList) }}">{{ __('account.operator_price_lists.assignments_edit_prices') }}</a>
                                        <button type="submit" class="btn btn-primary">{{ __('account.operator_price_lists.assignments_save') }}</button>
                                    </div>
                                </form>
                            @else
                                <p class="text-muted mb-3">{{ __('account.operator_price_lists.assignments_no_agencies') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-light" href="{{ route('account.operator-price-lists.index') }}">{{ __('account.operator_price_lists.assignments_cancel') }}</a>
                                    <a class="btn btn-outline-secondary" href="{{ route('account.operator-price-lists.edit', $priceList) }}">{{ __('account.operator_price_lists.assignments_edit_prices') }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($hasAgencies)
        <script>
            (function () {
                const assignmentsBody = document.getElementById('assignments-body');
                const addAssignmentBtn = document.getElementById('btn-add-assignment');
                if (!assignmentsBody || !addAssignmentBtn) {
                    return;
                }

                const agencyOptionsHtml = @json(collect($agencyOptions)->map(
                    fn ($label, $id) => '<option value="'.e((string) $id).'">'.e($label).'</option>'
                )->implode(''));

                const agencyPlaceholder = @json(__('account.operator_price_lists.fields.agency_placeholder'));
                const adjustmentNone = @json(__('account.operator_price_lists.assignment_adjustment.none'));
                const adjustmentPercentage = @json(__('account.operator_price_lists.assignment_adjustment.percentage'));
                const adjustmentFixed = @json(__('account.operator_price_lists.assignment_adjustment.fixed'));
                const adjustmentValuePlaceholder = @json(__('account.operator_price_lists.fields.adjustment_value_placeholder'));
                const removeAssignmentLabel = @json(__('account.operator_price_lists.remove_assignment_button'));
                const priceStep = @json($priceStep);

                function assignmentRowsCount() {
                    return assignmentsBody.querySelectorAll('tr').length;
                }

                function refreshAssignmentIndexes() {
                    const rows = assignmentsBody.querySelectorAll('tr');
                    rows.forEach((row, index) => {
                        row.querySelectorAll('[name]').forEach((input) => {
                            const currentName = input.getAttribute('name');
                            if (!currentName) {
                                return;
                            }
                            const updatedName = currentName.replace(/assignments\[\d+\]/, 'assignments[' + index + ']');
                            input.setAttribute('name', updatedName);
                        });
                    });
                }

                function addAssignmentRow() {
                    const index = assignmentRowsCount();
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <select name="assignments[${index}][agency_account_id]" class="form-select">
                                <option value="">${agencyPlaceholder}</option>
                                ${agencyOptionsHtml}
                            </select>
                        </td>
                        <td>
                            <select name="assignments[${index}][adjustment_type]" class="form-select">
                                <option value="none">${adjustmentNone}</option>
                                <option value="percentage">${adjustmentPercentage}</option>
                                <option value="fixed">${adjustmentFixed}</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" step="${priceStep}" name="assignments[${index}][adjustment_value]" class="form-control" value="" placeholder="${adjustmentValuePlaceholder}">
                        </td>
                        <td>
                            <input type="date" name="assignments[${index}][valid_from]" class="form-control" value="">
                        </td>
                        <td>
                            <input type="date" name="assignments[${index}][valid_to]" class="form-control" value="">
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="hidden" name="assignments[${index}][is_active]" value="0">
                                <input class="form-check-input" type="checkbox" name="assignments[${index}][is_active]" value="1" checked>
                            </div>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-assignment">${removeAssignmentLabel}</button>
                        </td>
                    `;
                    assignmentsBody.appendChild(row);
                }

                addAssignmentBtn.addEventListener('click', addAssignmentRow);

                assignmentsBody.addEventListener('click', (event) => {
                    const target = event.target;
                    if (!(target instanceof HTMLElement) || !target.classList.contains('btn-remove-assignment')) {
                        return;
                    }
                    const row = target.closest('tr');
                    if (!row) {
                        return;
                    }
                    row.remove();
                    refreshAssignmentIndexes();
                });
            })();
        </script>
    @endif

    <x-site-footer-simple />

@endsection
