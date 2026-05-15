<div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold">{{ __('wizard.experiences_validation_heading') }}</div>
            <ul class="mb-0 mt-2 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($categoryOptions === [])
        <div class="alert alert-warning mb-0" role="alert">
            {{ __('wizard.experiences_no_catalog') }}
        </div>
    @else
        <p class="text-muted small mb-2">{{ __('wizard.experiences_intro') }}</p>

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <span class="text-muted small me-1">{{ __('wizard.experiences_category_bulk_hint') }}</span>
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
                            id="experience-cat-{{ $cid }}"
                        >
                        <label class="form-check-label" for="experience-cat-{{ $cid }}">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($groupedExperiences->isEmpty())
            <div class="alert alert-light border" role="alert">
                {{ __('wizard.experiences_none_for_filter') }}
            </div>
        @else
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <span class="text-muted small me-1">{{ __('wizard.experiences_bulk_hint') }}</span>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    wire:click="selectAllVisibleExperiences"
                    wire:loading.attr="disabled"
                    wire:target="selectAllVisibleExperiences,clearAllExperiences"
                >
                    {{ __('wizard.experiences_select_all') }}
                </button>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    wire:click="clearAllExperiences"
                    wire:loading.attr="disabled"
                    wire:target="selectAllVisibleExperiences,clearAllExperiences"
                >
                    {{ __('wizard.experiences_select_none') }}
                </button>
            </div>

            <div class="row g-3">
                @foreach ($groupedExperiences as $categoryId => $items)
                    @php
                        $category = $items->first()?->category;
                        $categoryTitle = $category && $category->name !== ''
                            ? $category->name
                            : ($category?->code ?? __('wizard.experiences_category_fallback'));
                    @endphp
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header py-2 bg-light">
                                <h6 class="mb-0">{{ $categoryTitle }}</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-2">
                                    @foreach ($items as $experience)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    wire:model.live="selectedExperienceIds"
                                                    value="{{ (string) $experience->id }}"
                                                    id="experience-{{ $experience->id }}"
                                                >
                                                <label class="form-check-label" for="experience-{{ $experience->id }}">
                                                    {{ $experience->name !== '' ? $experience->name : $experience->code }}
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
                <span wire:loading.remove wire:target="save">{{ __('wizard.experiences_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    @endif
</div>
