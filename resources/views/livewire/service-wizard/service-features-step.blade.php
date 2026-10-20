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
                                            $featureStates = [
                                                \App\Livewire\ServiceWizard\ServiceFeaturesStep::INCLUSION_UNSET => [
                                                    'label' => __('wizard.features_state_unset'),
                                                    'class' => 'btn-outline-secondary',
                                                ],
                                                \App\Models\ServiceFeature::INCLUSION_INCLUDED => [
                                                    'label' => __('wizard.features_state_included'),
                                                    'class' => 'btn-outline-success',
                                                ],
                                                \App\Models\ServiceFeature::INCLUSION_EXCLUDED => [
                                                    'label' => __('wizard.features_state_excluded'),
                                                    'class' => 'btn-outline-danger',
                                                ],
                                            ];
                                        @endphp
                                        <div class="col-lg-6" wire:key="feature-{{ $feature->id }}">
                                            <div class="border rounded p-2 h-100">
                                                <div class="small mb-2">{{ $featureLabel }}</div>
                                                <div class="btn-group btn-group-sm w-100" role="group" aria-label="{{ $featureLabel }}">
                                                    @foreach ($featureStates as $state => $stateMeta)
                                                        <input
                                                            type="radio"
                                                            class="btn-check"
                                                            name="feature-inclusion-{{ $feature->id }}"
                                                            id="feature-{{ $feature->id }}-{{ $state }}"
                                                            value="{{ $state }}"
                                                            wire:model.live="featureInclusions.{{ $feature->id }}"
                                                            autocomplete="off"
                                                        >
                                                        <label class="btn {{ $stateMeta['class'] }}" for="feature-{{ $feature->id }}-{{ $state }}">
                                                            {{ $stateMeta['label'] }}
                                                        </label>
                                                    @endforeach
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
        @endif

        <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">{{ __('wizard.features_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    @endif
</div>
