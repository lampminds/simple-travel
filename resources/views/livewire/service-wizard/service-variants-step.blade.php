@php
    use App\Models\ServiceVariant;
@endphp
<div>
    @if ($flashMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $flashMessage }}
            <button type="button" class="btn-close" wire:click="$set('flashMessage', null)" aria-label="{{ __('filament.common.close') }}"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <h6 class="mb-0">{{ __('wizard.variants_list_heading') }}</h6>
            <x-catalog-helper-icon
                :html="$catalogVariantFieldHelpHtml['description'] ?? null"
                trigger-id="step4-catalog-helper-variant-description"
                content-id="step4-catalog-helper-variant-description-html"
                :aria-label="__('wizard.catalog_helper.aria_label_variant')"
            />
        </div>
        <button type="button" class="btn btn-sm btn-primary" wire:click="requestStartCreate">
            {{ __('wizard.variants_new') }}
        </button>
    </div>

    @if ($variants->isEmpty())
        <div class="alert alert-light border" role="status">
            {{ __('wizard.variants_none') }}
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white border rounded mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 4rem;">{{ __('wizard.variants_col_thumb') }}</th>
                        <th scope="col">{{ __('wizard.variants_col_sku') }}</th>
                        <th scope="col">{{ __('wizard.variants_col_name') }}</th>
                        <th scope="col">{{ __('wizard.variants_col_status') }}</th>
                        <th scope="col">{{ __('wizard.variants_col_price') }}</th>
                        <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variants as $variant)
                        @php
                            $thumb = $variant->getFirstMedia(ServiceVariant::MEDIA_COLLECTION_MAIN);
                        @endphp
                        <tr>
                            <td class="text-center">
                                @if ($thumb)
                                    <img
                                        src="{{ $thumb->getAvailableUrl([ServiceVariant::MEDIA_CONVERSION_THUMBNAIL]) }}"
                                        alt=""
                                        class="rounded border"
                                        style="width: 40px; height: 40px; object-fit: cover;"
                                    >
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="fw-medium">{{ $variant->sku }}</td>
                            <td>{{ $variant->name !== '' ? $variant->name : '—' }}</td>
                            <td>{{ __('filament.resources.service_variant_status.'.$variant->status) }}</td>
                            <td>{{ $this->formatVariantBasePrice($variant) }}</td>
                            <td class="text-end text-nowrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary me-1"
                                    wire:click="requestCopyFrom({{ $variant->id }})"
                                    title="{{ __('wizard.variants_copy_hint') }}"
                                >
                                    {{ __('wizard.variants_copy') }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary me-1"
                                    wire:click="requestStartEdit({{ $variant->id }})"
                                >
                                    {{ __('wizard.variants_edit') }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="if (!confirm(@js(__('wizard.variants_delete_confirm')))) { return false; } $wire.deleteVariant({{ $variant->id }})"
                                >
                                    {{ __('wizard.variants_delete') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($showVariantFormModal)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            aria-labelledby="wizardVariantFormModalTitle"
            wire:key="wizard-variant-form-modal-{{ $editingVariantId ?? 'new' }}"
            style="background-color: rgba(33, 37, 41, 0.5);"
            wire:click.self="requestCancel"
        >
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" wire:click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center flex-wrap gap-2 mb-0" id="wizardVariantFormModalTitle">
                            @if ($editingVariantId)
                                {{ __('wizard.variants_form_edit_title') }}
                            @elseif ($isCopy)
                                {{ __('wizard.variants_form_copy_title') }}
                            @else
                                {{ __('wizard.variants_form_create_title') }}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="requestCancel" aria-label="{{ __('filament.common.close') }}"></button>
                    </div>
                    <div class="modal-body">
                        @include('livewire.service-wizard.partials.variant-form-fields')
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showDiscardConfirm)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            aria-labelledby="wizardVariantDiscardModalTitle"
            style="background-color: rgba(33, 37, 41, 0.65); z-index: 1065;"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mb-0" id="wizardVariantDiscardModalTitle">
                            {{ __('wizard.variants_discard_confirm_title') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="dismissDiscardConfirm" aria-label="{{ __('filament.common.close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">{{ __('wizard.variants_discard_confirm') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="dismissDiscardConfirm">
                            {{ __('wizard.variants_discard_confirm_no') }}
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="confirmDiscard">
                            {{ __('wizard.variants_discard_confirm_yes') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
