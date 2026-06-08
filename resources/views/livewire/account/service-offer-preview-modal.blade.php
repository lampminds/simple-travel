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
                            <div class="text-muted text-uppercase fs-12 mb-1">{{ __('account.service_offers.operator_preview_modal_title') }}</div>
                            @if (is_array($preview))
                                <h5 class="modal-title mb-0">{{ $preview['title'] ?? '' }}</h5>
                            @endif
                        </div>
                        <button type="button" class="btn-close" aria-label="{{ __('account.service_offers.operator_preview_close') }}" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        @if ($loading || ! is_array($preview))
                            <div class="text-center text-muted py-5">
                                {{ __('account.service_offers.operator_preview_loading') }}
                            </div>
                        @else
                            @include('account.service-offers.operator.partials.preview-body', [
                                'preview' => $preview,
                                'forPdf' => false,
                            ])
                        @endif
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        @if ($offerUuid !== null)
                            <div class="d-flex flex-wrap gap-2 me-auto">
                                <a
                                    href="{{ route('account.service-offers.preview-pdf', ['offer' => $offerUuid, 'photos' => 1]) }}"
                                    class="btn btn-outline-primary"
                                    target="_blank"
                                    rel="noopener"
                                    title="{{ __('account.service_offers.operator_preview_export_pdf_with_photos') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M14 14H2V2h5V0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9h-2z"/>
                                        <path d="M9 0v2h2.586L6.707 6.879 8.121 8.293 13 3.414V6h2V0z"/>
                                    </svg>
                                    {{ __('account.service_offers.operator_preview_export_pdf_with_photos') }}
                                </a>
                                <a
                                    href="{{ route('account.service-offers.preview-pdf', ['offer' => $offerUuid, 'photos' => 0]) }}"
                                    class="btn btn-outline-secondary"
                                    target="_blank"
                                    rel="noopener"
                                    title="{{ __('account.service_offers.operator_preview_export_pdf_without_photos') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M14 14H2V2h5V0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9h-2z"/>
                                        <path d="M9 0v2h2.586L6.707 6.879 8.121 8.293 13 3.414V6h2V0z"/>
                                    </svg>
                                    {{ __('account.service_offers.operator_preview_export_pdf_without_photos') }}
                                </a>
                            </div>
                        @endif
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">
                            {{ __('account.service_offers.operator_preview_close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
