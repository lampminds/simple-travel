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

    @if ($experiences->isEmpty())
        <div class="alert alert-warning mb-0" role="alert">
            {{ __('wizard.experiences_no_catalog') }}
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('wizard.experiences_intro') }}</p>

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <span class="text-muted small me-1">{{ __('wizard.experiences_bulk_hint') }}</span>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                wire:click="selectAllExperiences"
                wire:loading.attr="disabled"
                wire:target="selectAllExperiences,clearAllExperiences"
            >
                {{ __('wizard.experiences_select_all') }}
            </button>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                wire:click="clearAllExperiences"
                wire:loading.attr="disabled"
                wire:target="selectAllExperiences,clearAllExperiences"
            >
                {{ __('wizard.experiences_select_none') }}
            </button>
        </div>

        <div class="row g-2 mb-4">
            @foreach ($experiences as $experience)
                <div class="col-6 col-md-4 col-lg-3">
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

        <div class="mt-4">
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">{{ __('wizard.experiences_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    @endif
</div>
