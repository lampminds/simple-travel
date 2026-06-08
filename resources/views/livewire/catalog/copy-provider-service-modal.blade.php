<div>
    @if ($showCopyModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background-color: rgba(0, 0, 0, 0.45);"
            wire:keydown.escape.window="closeCopyModal"
        >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content" wire:click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('wizard.service_copy_modal_title') }}</h5>
                        <button type="button" class="btn-close" aria-label="{{ __('wizard.service_copy_cancel') }}" wire:click="closeCopyModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            {{ __('wizard.service_copy_modal_intro', ['name' => $copySourceLabel]) }}
                        </p>

                        @error('copySections')
                            <div class="alert alert-danger py-2 small" role="alert">{{ $message }}</div>
                        @enderror

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="selectAllCopySections">
                                {{ __('wizard.service_copy_select_all') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="selectNoCopySections">
                                {{ __('wizard.service_copy_select_none') }}
                            </button>
                        </div>

                        <fieldset class="mb-0">
                            <legend class="visually-hidden">{{ __('wizard.service_copy_sections_legend') }}</legend>
                            @foreach ($this->availableCopySections() as $section)
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="copy-section-{{ $section }}"
                                        wire:model.live="copySections.{{ $section }}"
                                    >
                                    <label class="form-check-label" for="copy-section-{{ $section }}">
                                        {{ $this->copySectionLabel($section) }}
                                    </label>
                                </div>
                            @endforeach
                        </fieldset>

                        <p class="text-muted small mt-3 mb-0">{{ __('wizard.service_copy_images_hint') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeCopyModal">
                            {{ __('wizard.service_copy_cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="performCopy" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="performCopy">{{ __('wizard.service_copy_confirm') }}</span>
                            <span wire:loading wire:target="performCopy">{{ __('wizard.service_copy_working') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
