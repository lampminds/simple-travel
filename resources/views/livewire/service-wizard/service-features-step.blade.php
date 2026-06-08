<div>
    @if ($scopedCount === 0)
        <div class="alert alert-warning" role="alert">
            {{ __('wizard.features_no_scope') }}
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('wizard.features_help_categories') }}</p>

        @if ($groupedFeatures->isEmpty())
            <div class="alert alert-light border" role="alert">
                {{ __('wizard.features_none_for_filter') }}
            </div>
        @else
            <div class="accordion" id="features-accordion">
                @foreach ($groupedFeatures as $categoryId => $features)
                    @php
                        $category = $features->first()?->serviceFeatureCategory;
                        $categoryTitle = $category?->name !== '' && $category?->name !== null
                            ? $category->name
                            : ($category?->code ?? __('wizard.features_category_fallback'));
                        $collapseId = 'feature-cat-' . (int) $categoryId;
                        $headingId = 'heading-' . $collapseId;
                        $isOpen = in_array((int) $categoryId, $openAccordionCategoryIds, true);
                    @endphp
                    <div class="accordion-item" wire:key="feature-accordion-{{ $categoryId }}">
                        <h2 class="accordion-header" id="{{ $headingId }}">
                            <button
                                class="accordion-button {{ $isOpen ? '' : 'collapsed' }}"
                                type="button"
                                wire:click="toggleAccordion({{ (int) $categoryId }})"
                                aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}"
                            >
                                {{ $categoryTitle }}
                            </button>
                        </h2>
                        <div
                            id="{{ $collapseId }}"
                            class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}"
                            aria-labelledby="{{ $headingId }}"
                        >
                            <div class="accordion-body">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span class="text-muted small me-1">{{ __('wizard.features_bulk_hint') }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="selectAllFeaturesInCategory({{ (int) $categoryId }})"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllFeaturesInCategory,clearFeaturesInCategory"
                                    >
                                        {{ __('wizard.features_select_all') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearFeaturesInCategory({{ (int) $categoryId }})"
                                        wire:loading.attr="disabled"
                                        wire:target="selectAllFeaturesInCategory,clearFeaturesInCategory"
                                    >
                                        {{ __('wizard.features_select_none') }}
                                    </button>
                                </div>
                                <div class="row g-2">
                                    @foreach ($features as $feature)
                                        <div class="col-md-6 col-lg-4" wire:key="feature-{{ $feature->id }}">
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
