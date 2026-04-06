<div>
    @if ($scopedCount === 0)
        <div class="alert alert-warning" role="alert">
            {{ __('wizard.features_no_scope') }}
        </div>
    @else
        <p class="text-muted small mb-2">{{ __('wizard.features_help_categories') }}</p>

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <span class="text-muted small me-1">{{ __('wizard.category_bulk_hint') }}</span>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                wire:click="selectAllCategories"
                wire:loading.attr="disabled"
                wire:target="selectAllCategories,clearAllCategories"
            >
                {{ __('wizard.category_select_all') }}
            </button>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                wire:click="clearAllCategories"
                wire:loading.attr="disabled"
                wire:target="selectAllCategories,clearAllCategories"
            >
                {{ __('wizard.category_select_none') }}
            </button>
        </div>

        <div class="row g-2 mb-4">
            @foreach ($categoryOptions as $cid => $label)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model.live="categoryIds"
                            value="{{ $cid }}"
                            id="cat-{{ $cid }}"
                        >
                        <label class="form-check-label" for="cat-{{ $cid }}">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($groupedFeatures->isEmpty())
            <div class="alert alert-light border" role="alert">
                {{ __('wizard.features_none_for_filter') }}
            </div>
        @else
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <span class="text-muted small me-1">{{ __('wizard.features_bulk_hint') }}</span>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    wire:click="selectAllVisibleFeatures"
                    wire:loading.attr="disabled"
                    wire:target="selectAllVisibleFeatures,clearAllFeatures"
                >
                    {{ __('wizard.features_select_all') }}
                </button>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    wire:click="clearAllFeatures"
                    wire:loading.attr="disabled"
                    wire:target="selectAllVisibleFeatures,clearAllFeatures"
                >
                    {{ __('wizard.features_select_none') }}
                </button>
            </div>

            <div class="row g-3">
                @foreach ($groupedFeatures as $categoryId => $features)
                    @php
                        $category = $features->first()?->serviceFeatureCategory;
                        $categoryTitle = $category?->name !== '' && $category?->name !== null
                            ? $category->name
                            : ($category?->code ?? __('wizard.features_category_fallback'));
                    @endphp
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header py-2 bg-light">
                                <h6 class="mb-0">{{ $categoryTitle }}</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-2">
                                    @foreach ($features as $feature)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    wire:model.live="selectedFeatureIds"
                                                    value="{{ (string) $feature->id }}"
                                                    id="feature-{{ $feature->id }}"
                                                >
                                                <label class="form-check-label" for="feature-{{ $feature->id }}">
                                                    {{ $feature->name !== '' ? $feature->name : $feature->code }}
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
        @endif

        <div class="mt-4">
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">{{ __('wizard.features_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    @endif
</div>
