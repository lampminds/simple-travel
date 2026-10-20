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
                                        wire:click="includeAllFeaturesInCategory({{ (int) $categoryId }})"
                                        wire:loading.attr="disabled"
                                        wire:target="includeAllFeaturesInCategory,clearFeaturesInCategory"
                                    >
                                        {{ __('wizard.features_include_all') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearFeaturesInCategory({{ (int) $categoryId }})"
                                        wire:loading.attr="disabled"
                                        wire:target="includeAllFeaturesInCategory,clearFeaturesInCategory"
                                    >
                                        {{ __('wizard.features_clear') }}
                                    </button>
                                </div>
                                <div class="row g-2">
                                    @foreach ($features as $feature)
                                        @php
                                            $featureLabel = $feature->name !== '' ? $feature->name : $feature->code;
                                            $currentInclusion = $featureInclusions[(string) $feature->id]
                                                ?? $featureInclusions[$feature->id]
                                                ?? \App\Livewire\ServiceWizard\ServiceFeaturesStep::INCLUSION_UNSET;
                                        @endphp
                                        <div class="col-md-6 col-xl-4" wire:key="feature-{{ $feature->id }}">
                                            <label class="d-flex align-items-center justify-content-between gap-2 border rounded px-2 py-1 mb-0 h-100" for="feature-inclusion-{{ $feature->id }}">
                                                <span class="small">{{ $featureLabel }}</span>
                                                <select
                                                    id="feature-inclusion-{{ $feature->id }}"
                                                    class="form-select form-select-sm w-auto flex-shrink-0 @if ($currentInclusion === \App\Models\ServiceFeature::INCLUSION_INCLUDED) border-success @elseif ($currentInclusion === \App\Models\ServiceFeature::INCLUSION_EXCLUDED) border-danger @endif"
                                                    style="min-width: 11rem;"
                                                    wire:model.live="featureInclusions.{{ $feature->id }}"
                                                >
                                                    <option value="{{ \App\Livewire\ServiceWizard\ServiceFeaturesStep::INCLUSION_UNSET }}">➖ {{ __('wizard.features_state_unset') }}</option>
                                                    <option value="{{ \App\Models\ServiceFeature::INCLUSION_INCLUDED }}">✅ {{ __('wizard.features_state_included') }}</option>
                                                    <option value="{{ \App\Models\ServiceFeature::INCLUSION_EXCLUDED }}">⛔ {{ __('wizard.features_state_excluded') }}</option>
                                                </select>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">{{ __('wizard.features_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    @endif
</div>
