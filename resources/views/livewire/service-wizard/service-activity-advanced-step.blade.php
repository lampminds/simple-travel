<div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold">{{ __('wizard.step7_activity_validation_heading') }}</div>
            <ul class="mb-0 mt-2 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($saveMessage)
        <div class="alert alert-success py-2 small" role="status">{{ $saveMessage }}</div>
    @endif

    @if ($categories->isEmpty())
        <div class="alert alert-warning mb-0" role="alert">
            {{ __('wizard.step7_activity_no_catalog') }}
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('wizard.step7_activity_intro') }}</p>

        <fieldset class="border rounded-2 px-3 py-2 bg-light mb-3">
            <legend class="float-none w-auto px-1 fs-6 mb-2">{{ __('wizard.step7_activity_options_legend') }}</legend>
            <div class="row row-cols-1 row-cols-sm-3 g-2">
                <div class="col">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="activity-guide"
                            wire:model="guide_included"
                        >
                        <label class="form-check-label" for="activity-guide">{{ __('wizard.step7_activity_field_guide') }}</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="activity-transport"
                            wire:model="transport_included"
                        >
                        <label class="form-check-label" for="activity-transport">{{ __('wizard.step7_activity_field_transport') }}</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-check mb-0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="activity-outdoor"
                            wire:model="outdoor_activity"
                        >
                        <label class="form-check-label" for="activity-outdoor">{{ __('wizard.step7_activity_field_outdoor') }}</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="border rounded-2 px-3 py-3 mb-3">
            <legend class="float-none w-auto px-1 fs-6 mb-2">{{ __('wizard.step7_activity_field_types') }}</legend>
            <p class="text-muted small mb-2">{{ __('wizard.step7_activity_types_help') }}</p>

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <span class="text-muted small me-1">{{ __('wizard.step7_activity_catalog_bulk_hint') }}</span>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    wire:click="selectAllCatalogTypes"
                    wire:loading.attr="disabled"
                    wire:target="selectAllCatalogTypes,clearAllCatalogTypes,selectAllTypesInCategory,clearTypesInCategory"
                >
                    {{ __('wizard.features_select_all') }}
                </button>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    wire:click="clearAllCatalogTypes"
                    wire:loading.attr="disabled"
                    wire:target="selectAllCatalogTypes,clearAllCatalogTypes,selectAllTypesInCategory,clearTypesInCategory"
                >
                    {{ __('wizard.features_select_none') }}
                </button>
            </div>

            <div class="row g-3">
                @foreach ($categories as $category)
                    @php
                        $categoryTitle = $category->name !== '' ? $category->name : $category->code;
                    @endphp
                    <div class="col-12" wire:key="activity-cat-{{ $category->id }}">
                        <div class="card border">
                            <div class="card-header py-2 bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="mb-0">{{ $categoryTitle }}</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="selectAllTypesInCategory({{ (int) $category->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllCatalogTypes,clearAllCatalogTypes,selectAllTypesInCategory,clearTypesInCategory"
                                    >
                                        {{ __('wizard.features_select_all') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearTypesInCategory({{ (int) $category->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllCatalogTypes,clearAllCatalogTypes,selectAllTypesInCategory,clearTypesInCategory"
                                    >
                                        {{ __('wizard.features_select_none') }}
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-2">
                                    @foreach ($category->activityTypes as $type)
                                        <div class="col-md-6 col-lg-4" wire:key="activity-type-{{ $type->id }}">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input @error('activityTypeIds') is-invalid @enderror @error('activityTypeIds.*') is-invalid @enderror"
                                                    type="checkbox"
                                                    id="activity-type-{{ $type->id }}"
                                                    value="{{ (string) $type->id }}"
                                                    wire:model.live="activityTypeIds"
                                                >
                                                <label class="form-check-label" for="activity-type-{{ $type->id }}">
                                                    {{ $type->name !== '' ? $type->name : $type->code }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @error('activityTypeIds')
                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
            @enderror
            @error('activityTypeIds.*')
                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
            @enderror
        </fieldset>

        <div class="d-flex justify-content-end mt-3">
            <button
                type="button"
                class="btn btn-primary"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">{{ __('wizard.step7_activity_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status"></span>
            </button>
        </div>
    @endif
</div>
