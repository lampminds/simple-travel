@php
    use App\Models\ServiceVariant;
@endphp
<div>
    @if ($flashMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $flashMessage }}
            <button type="button" class="btn-close" wire:click="$set('flashMessage', null)" aria-label="Close"></button>
        </div>
    @endif

    @if ($mode === 'list')
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h6 class="mb-0">{{ __('wizard.variants_list_heading') }}</h6>
            <button type="button" class="btn btn-sm btn-primary" wire:click="startCreate">
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
                                <td>
                                    {{ number_format((float) $variant->base_price, 2, ',', ' ') }}
                                    {{ $variant->currency?->display_name ?? '' }}
                                </td>
                                <td class="text-end text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary me-1"
                                        wire:click="copyFrom({{ $variant->id }})"
                                        title="{{ __('wizard.variants_copy_hint') }}"
                                    >
                                        {{ __('wizard.variants_copy') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        wire:click="startEdit({{ $variant->id }})"
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
    @else
        <div class="mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancel">
                {{ __('wizard.variants_back_to_list') }}
            </button>
        </div>

        <div class="card border">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    @if ($editingVariantId)
                        {{ __('wizard.variants_form_edit_title') }}
                    @elseif ($isCopy)
                        {{ __('wizard.variants_form_copy_title') }}
                    @else
                        {{ __('wizard.variants_form_create_title') }}
                    @endif
                </h6>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small mb-3" role="alert">
                        {{ __('wizard.variants_validation_tabs_hint') }}
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <ul class="nav nav-tabs flex-wrap mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            type="button"
                            class="nav-link @if ($variantFormTab === 'general') active @endif @if ($this->variantTabHasError('general')) text-danger fw-semibold @endif"
                            wire:click="$set('variantFormTab', 'general')"
                        >
                            {{ __('wizard.variants_tab_general') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            type="button"
                            class="nav-link @if ($variantFormTab === 'pricing') active @endif @if ($this->variantTabHasError('pricing')) text-danger fw-semibold @endif"
                            wire:click="$set('variantFormTab', 'pricing')"
                        >
                            {{ __('wizard.variants_tab_pricing') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            type="button"
                            class="nav-link @if ($variantFormTab === 'descriptions') active @endif @if ($this->variantTabHasError('descriptions')) text-danger fw-semibold @endif"
                            wire:click="$set('variantFormTab', 'descriptions')"
                        >
                            {{ __('wizard.variants_tab_descriptions') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            type="button"
                            class="nav-link @if ($variantFormTab === 'images') active @endif @if ($this->variantTabHasError('images')) text-danger fw-semibold @endif"
                            wire:click="$set('variantFormTab', 'images')"
                        >
                            {{ __('wizard.variants_tab_images') }}
                        </button>
                    </li>
                </ul>

                @if ($variantFormTab === 'general')
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label required-label">{{ __('filament.resources.service_variant_fields.sku') }}</label>
                            <input type="text" class="form-control @error('form.sku') is-invalid @enderror" wire:model.blur="form.sku">
                            @error('form.sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.status') }}</label>
                            <select class="form-select @error('form.status') is-invalid @enderror" wire:model.live="form.status">
                                @foreach (['active', 'suspended', 'discontinued', 'inactive', 'hidden'] as $st)
                                    <option value="{{ $st }}">{{ __('wizard.variant_status.'.$st) }}</option>
                                @endforeach
                            </select>
                            @error('form.status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.inventory_type') }}</label>
                            <select class="form-select" wire:model.live="form.inventory_type">
                                @foreach (['unlimited', 'per_day', 'per_timeslot', 'per_departure'] as $it)
                                    <option value="{{ $it }}">{{ __('wizard.variant_inventory.'.$it) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.inventory_total') }}</label>
                            <input
                                type="number"
                                min="0"
                                class="form-control @error('form.inventory_total') is-invalid @enderror"
                                wire:model.blur="form.inventory_total"
                                @if(($form['inventory_type'] ?? '') === 'unlimited') disabled @endif
                            >
                            @error('form.inventory_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.capacity_min') }}</label>
                            <input type="number" min="0" class="form-control" wire:model.blur="form.capacity_min">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.capacity_max') }}</label>
                            <input type="number" min="0" class="form-control" wire:model.blur="form.capacity_max">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.min_advance_booking_hours') }}</label>
                            <input type="number" min="0" class="form-control" wire:model.blur="form.min_advance_booking_hours">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.max_advance_booking_days') }}</label>
                            <input type="number" min="0" class="form-control" wire:model.blur="form.max_advance_booking_days">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.start_time') }}</label>
                            <input type="time" class="form-control @error('form.start_time') is-invalid @enderror" wire:model.blur="form.start_time">
                            @error('form.start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.end_time') }}</label>
                            <input type="time" class="form-control @error('form.end_time') is-invalid @enderror" wire:model.blur="form.end_time">
                            @error('form.end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                @elseif ($variantFormTab === 'pricing')
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.pricing_type') }}</label>
                            <select class="form-select" wire:model.live="form.pricing_type">
                                @foreach (['per_person', 'per_unit', 'per_room', 'per_vehicle', 'per_group'] as $pt)
                                    <option value="{{ $pt }}">{{ __('wizard.variant_pricing.'.$pt) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.base_price') }}</label>
                            <input type="text" inputmode="decimal" class="form-control @error('form.base_price') is-invalid @enderror" wire:model.blur="form.base_price">
                            @error('form.base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('filament.resources.service_variant_fields.currency') }}</label>
                            <select class="form-select @error('form.currency_id') is-invalid @enderror" wire:model.live="form.currency_id">
                                <option value="">—</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->display_name }}</option>
                                @endforeach
                            </select>
                            @error('form.currency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                @elseif ($variantFormTab === 'descriptions')
                    <p class="text-muted small mb-3">{{ __('wizard.variants_descriptions_help') }}</p>
                    <div class="row g-3">
                        @foreach ($languages as $language)
                            <div class="col-lg-6" wire:key="variant-lang-{{ $language->id }}">
                                <div class="border rounded p-3 bg-white h-100">
                                    <h6 class="mb-3">{{ $language->display_name }}</h6>
                                    <div class="mb-3">
                                        <label class="form-label required-label">{{ __('wizard.variants_translation_name') }}</label>
                                        <input
                                            type="text"
                                            class="form-control @error('form.translations.'.$language->id.'.name') is-invalid @enderror"
                                            wire:model.blur="form.translations.{{ $language->id }}.name"
                                        >
                                        @error('form.translations.'.$language->id.'.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('wizard.variants_translation_description') }}</label>
                                        <textarea
                                            class="form-control @error('form.translations.'.$language->id.'.description') is-invalid @enderror"
                                            rows="4"
                                            wire:model.blur="form.translations.{{ $language->id }}.description"
                                        ></textarea>
                                        @error('form.translations.'.$language->id.'.description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-3">{{ __('wizard.variant_media_help') }}</p>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="small text-uppercase text-muted mb-2">{{ __('wizard.variant_media_main_heading') }}</h6>
                            @if ($editingVariantId && $variantMainMedia)
                                <div class="mb-3">
                                    <img
                                        src="{{ $variantMainMedia->getAvailableUrl([ServiceVariant::MEDIA_CONVERSION_SMALL]) }}"
                                        alt=""
                                        class="img-fluid rounded border"
                                        style="max-height: 200px; object-fit: contain;"
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm mb-3"
                                    wire:click="removeVariantMainImage"
                                    wire:confirm="{{ __('wizard.variant_media_main_remove_confirm') }}"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('wizard.variant_media_main_remove') }}
                                </button>
                            @endif
                            <div class="mb-2">
                                <input type="file" class="form-control form-control-sm" accept="image/*" wire:model="mainImage">
                                @error('mainImage')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div wire:loading wire:target="mainImage" class="text-muted small">{{ __('wizard.media_uploading') }}</div>
                            @if (! $editingVariantId)
                                <p class="small text-muted mb-0">{{ __('wizard.variant_media_new_variant_hint') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="small text-uppercase text-muted mb-2">{{ __('wizard.variant_media_gallery_heading') }}</h6>
                            @if ($editingVariantId && $variantGalleryMedia->isNotEmpty())
                                <ul class="list-group list-group-flush border rounded mb-3">
                                    @foreach ($variantGalleryMedia as $item)
                                        <li class="list-group-item d-flex align-items-center gap-2 py-2">
                                            <img
                                                src="{{ $item->getAvailableUrl([ServiceVariant::MEDIA_CONVERSION_THUMBNAIL]) }}"
                                                alt=""
                                                class="rounded border flex-shrink-0"
                                                style="width: 48px; height: 48px; object-fit: cover;"
                                            >
                                            <span class="small text-truncate flex-grow-1">{{ $item->name ?: $item->file_name }}</span>
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                wire:click="removeVariantGalleryMedia({{ (int) $item->id }})"
                                                wire:confirm="{{ __('wizard.variant_media_gallery_remove_confirm') }}"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ __('wizard.media_gallery_remove') }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mb-2">
                                <input
                                    type="file"
                                    class="form-control form-control-sm"
                                    accept="image/*"
                                    wire:model="galleryImages"
                                    multiple
                                >
                                @error('galleryImages')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('galleryImages.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div wire:loading wire:target="galleryImages" class="text-muted small">{{ __('wizard.media_uploading') }}</div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 pt-2 border-top">
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ __('wizard.variants_save') }}</span>
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
