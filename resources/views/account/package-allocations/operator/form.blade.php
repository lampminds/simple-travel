@php
    use App\Models\PackageAllocation;

    $isEdit = $allocation !== null;
    $agencyLabel = $agency->commercial_name ?? $agency->name ?? ('#' . $agency->id);
    $catalogOptions = $targetOptions['catalogs'] ?? [];
    $hasTargets = count($catalogOptions) > 0;
    $selectedTargetKey = $selectedTargetKey ?? '';

    $allocationType = old('allocation_type', $isEdit ? $allocation->allocation_type : PackageAllocation::TYPE_HARD);
    $capacityValue = old('capacity', $isEdit && $allocation->allocation_type !== PackageAllocation::TYPE_FREE_SALE ? (string) $allocation->capacity : '');
    $startDate = old('start_date', $isEdit && $allocation->start_date ? normalize_form_date_value($allocation->start_date) : '');
    $endDate = old('end_date', $isEdit && $allocation->end_date ? normalize_form_date_value($allocation->end_date) : '');
    $isActive = old('active', $isEdit ? $allocation->active : true);
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.package_allocations.edit_page_title') : __('account.package_allocations.create_page_title')])

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
                        :title="$isEdit ? __('account.package_allocations.edit_heading') : __('account.package_allocations.create_heading')"
                        :subtitle="$agencyLabel"
                        :instructions="__('account.package_allocations.form_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($hasTargets)
                                <form method="POST" action="{{ $submitRoute }}">
                                    @csrf
                                    @if ($submitMethod !== 'POST')
                                        @method($submitMethod)
                                    @endif

                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label for="target_key" class="form-label">{{ __('account.package_allocations.fields.target') }}</label>
                                            <select name="target_key" id="target_key" class="form-select" required>
                                                <option value="">{{ __('account.package_allocations.fields.target_placeholder') }}</option>
                                                @foreach ($catalogOptions as $catalogId => $catalogLabel)
                                                    <option value="catalog:{{ $catalogId }}" @selected((string) $selectedTargetKey === 'catalog:'.$catalogId)>
                                                        {{ $catalogLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">{{ __('account.package_allocations.fields.target_help') }}</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="allocation_type" class="form-label">{{ __('account.package_allocations.fields.allocation_type') }}</label>
                                            <select name="allocation_type" id="allocation_type" class="form-select" required>
                                                @foreach ([PackageAllocation::TYPE_HARD, PackageAllocation::TYPE_SOFT, PackageAllocation::TYPE_FREE_SALE] as $type)
                                                    <option value="{{ $type }}" @selected($allocationType === $type)>
                                                        {{ __('account.package_allocations.types.' . $type) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4" id="capacity-field-wrap">
                                            <label for="capacity" class="form-label">{{ __('account.package_allocations.fields.capacity') }}</label>
                                            <input
                                                type="number"
                                                min="1"
                                                step="1"
                                                name="capacity"
                                                id="capacity"
                                                class="form-control"
                                                value="{{ $capacityValue }}"
                                            >
                                            <div class="form-text">{{ __('account.package_allocations.fields.capacity_help') }}</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="start_date" class="form-label">{{ __('account.package_allocations.fields.start_date') }}</label>
                                            <x-locale-date-input name="start_date" id="start_date" :value="$startDate" />
                                        </div>

                                        <div class="col-md-4">
                                            <label for="end_date" class="form-label">{{ __('account.package_allocations.fields.end_date') }}</label>
                                            <x-locale-date-input name="end_date" id="end_date" :value="$endDate" />
                                            <div class="form-text">{{ __('account.package_allocations.fields.validity_help') }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="active" value="0">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="active"
                                                    id="active"
                                                    value="1"
                                                    @checked(filter_var($isActive, FILTER_VALIDATE_BOOLEAN))
                                                >
                                                <label class="form-check-label" for="active">{{ __('account.package_allocations.fields.active') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-4">
                                        <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.package_allocations.cancel_button') }}</a>
                                        <a class="btn btn-outline-secondary" href="{{ route('account.package-offers.agencies.edit', $agency) }}">
                                            {{ __('account.package_allocations.manage_offers_link') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            {{ $isEdit ? __('account.package_allocations.update_button') : __('account.package_allocations.save_button') }}
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p class="text-muted mb-3">{{ __('account.package_allocations.no_accepted_targets') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.package_allocations.cancel_button') }}</a>
                                    <a class="btn btn-outline-secondary" href="{{ route('account.package-offers.agencies.edit', $agency) }}">
                                        {{ __('account.package_allocations.manage_offers_link') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($hasTargets)
        <script>
            (function () {
                const typeSelect = document.getElementById('allocation_type');
                const capacityWrap = document.getElementById('capacity-field-wrap');
                const capacityInput = document.getElementById('capacity');
                if (!typeSelect || !capacityWrap || !capacityInput) {
                    return;
                }

                const freeSaleType = @json(PackageAllocation::TYPE_FREE_SALE);

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

    <x-site-footer-simple />
@endsection
