@php
    /** @var array<int, string> $importCatalogCategoryOptions */
    /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, \App\Models\ServiceTransferVehicleType>> $importCatalogGrouped */
    $oldTemplateTypeIds = collect(old('template_type_ids', []) ?? [])
        ->map(fn ($v) => (int) $v)
        ->filter(fn (int $id) => $id > 0)
        ->flip();
    $categoryCheckedFromOld = [];
    foreach ($importCatalogGrouped as $catId => $typesInCat) {
        foreach ($typesInCat as $vt) {
            if ($oldTemplateTypeIds->has((int) $vt->id)) {
                $categoryCheckedFromOld[(int) $catId] = true;
            }
        }
    }
    $importInitiallyExpanded = $categoryCheckedFromOld !== [];
@endphp

<div
    class="modal fade"
    id="transferVehicleCatalogImportModal"
    tabindex="-1"
    aria-labelledby="transferVehicleCatalogImportModalLabel"
    aria-hidden="true"
>
    {{-- Avoid modal-dialog-centered with scrollable XL content: it clips the header on tall catalogs. --}}
    <div class="modal-dialog modal-xl modal-dialog-scrollable my-3 mx-auto">
        <form id="tvt-import-catalog-form" method="POST" action="{{ route('account.transfer-vehicle-types.import') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferVehicleCatalogImportModalLabel">
                        {{ __('account.transfer_vehicle_types.import_modal_title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('account.transfer_vehicle_types.import_close') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">{{ __('account.transfer_vehicle_types.import_modal_intro') }}</p>

                    @if ($importCatalogGrouped->isEmpty())
                        <div class="alert alert-warning mb-0" role="alert">
                            {{ __('account.transfer_vehicle_types.import_template_empty') }}
                        </div>
                    @else
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                            <span class="text-muted small me-1">{{ __('wizard.category_bulk_hint') }}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-tvt-import-action="select-all-categories">
                                {{ __('wizard.category_select_all') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-tvt-import-action="clear-all-categories">
                                {{ __('wizard.category_select_none') }}
                            </button>
                        </div>

                        <div class="row g-2 mb-4">
                            @foreach ($importCatalogCategoryOptions as $cid => $label)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input tvt-import-cat-filter"
                                            type="checkbox"
                                            value="{{ (string) $cid }}"
                                            id="tvt-import-cat-{{ $cid }}"
                                            @checked($categoryCheckedFromOld[(int) $cid] ?? false)
                                        >
                                        <label class="form-check-label" for="tvt-import-cat-{{ $cid }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-light border tvt-import-no-categories-msg mb-3 @if ($importInitiallyExpanded) d-none @endif" role="alert">
                            {{ __('wizard.transfer_bootstrap_no_categories_selected') }}
                        </div>

                        <div class="tvt-import-types-wrap @if (! $importInitiallyExpanded) d-none @endif">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <span class="text-muted small me-1">{{ __('wizard.features_bulk_hint') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-tvt-import-action="select-all-visible-types">
                                    {{ __('wizard.features_select_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-tvt-import-action="clear-all-types">
                                    {{ __('wizard.features_select_none') }}
                                </button>
                            </div>

                            <div class="row g-3">
                                @foreach ($importCatalogGrouped as $categoryId => $typesInCat)
                                    @php
                                        $categoryId = (int) $categoryId;
                                        $categoryTitle = $importCatalogCategoryOptions[$categoryId] ?? __('wizard.transfer_bootstrap_category_other');
                                    @endphp
                                    <div
                                        class="col-12 @if (! ($categoryCheckedFromOld[$categoryId] ?? false)) d-none @endif"
                                        data-tvt-import-category-block="{{ $categoryId }}"
                                    >
                                        <div class="card border">
                                            <div class="card-header py-2 bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                <h6 class="mb-0">{{ $categoryTitle }}</h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        data-tvt-import-action="select-all-in-category"
                                                        data-tvt-import-category="{{ $categoryId }}"
                                                    >
                                                        {{ __('wizard.features_select_all') }}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        data-tvt-import-action="clear-in-category"
                                                        data-tvt-import-category="{{ $categoryId }}"
                                                    >
                                                        {{ __('wizard.features_select_none') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row row-cols-1 row-cols-md-2 g-2">
                                                    @foreach ($typesInCat as $vt)
                                                        <div class="col">
                                                            <div class="border rounded bg-white p-3 h-100">
                                                                <div class="form-check m-0">
                                                                    <input
                                                                        class="form-check-input tvt-import-type-cb"
                                                                        type="checkbox"
                                                                        name="template_type_ids[]"
                                                                        id="tvt-import-vt-{{ $vt->id }}"
                                                                        value="{{ $vt->id }}"
                                                                        @checked(collect(old('template_type_ids', []))->contains(fn ($v) => (string) $v === (string) $vt->id))
                                                                    >
                                                                    <label class="form-check-label" for="tvt-import-vt-{{ $vt->id }}">
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
                                @endforeach
                            </div>
                        </div>

                        @error('template_type_ids')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
                <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('account.transfer_vehicle_types.import_close') }}
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        @if ($importCatalogGrouped->isEmpty()) disabled @endif
                    >
                        {{ __('account.transfer_vehicle_types.import_submit') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
