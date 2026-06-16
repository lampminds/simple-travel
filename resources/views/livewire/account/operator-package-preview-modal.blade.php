<div>
    @if ($showModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background-color: rgba(0, 0, 0, 0.45);"
            wire:keydown.escape.window="closeModal"
        >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content border-0 shadow" wire:click.stop>
                    <div class="modal-header border-bottom-0 pb-0">
                        <div class="pe-3">
                            <div class="text-muted text-uppercase fs-12 mb-1">{{ __('account.operator_packages.preview_modal_title') }}</div>
                            @if (is_array($preview))
                                <h5 class="modal-title mb-0">{{ $preview['title'] ?? '' }}</h5>
                            @endif
                        </div>
                        <button type="button" class="btn-close" aria-label="{{ __('account.operator_packages.preview_close') }}" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        @if ($loading || ! is_array($preview))
                            <div class="text-center text-muted py-5">
                                {{ __('account.operator_packages.preview_loading') }}
                            </div>
                        @else
                            @include('account.operator-packages.partials.preview-body', [
                                'preview' => $preview,
                            ])
                        @endif
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">
                            {{ __('account.operator_packages.preview_close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
