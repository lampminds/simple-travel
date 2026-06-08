@php
    use App\Models\ServiceVariant;
@endphp

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

<div @class(['d-none' => $variantFormTab !== 'general'])>
    <div class="row g-2">
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'sku',
                'label' => __('filament.resources.service_variant_fields.sku'),
                'required' => true,
            ])
            <input type="text" class="form-control @error('form.sku') is-invalid @enderror" wire:model.blur="form.sku">
            @error('form.sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'status',
                'label' => __('filament.resources.service_variant_fields.status'),
            ])
            <select class="form-select @error('form.status') is-invalid @enderror" wire:model.live="form.status">
                @foreach (['active', 'suspended', 'discontinued'] as $st)
                    <option value="{{ $st }}">{{ __('wizard.variant_status.'.$st) }}</option>
                @endforeach
            </select>
            @error('form.status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'inventory_type',
                'label' => __('filament.resources.service_variant_fields.inventory_type'),
            ])
            <select class="form-select @error('form.inventory_type') is-invalid @enderror" wire:model.live="form.inventory_type">
                @foreach (['unlimited', 'per_day', 'per_timeslot', 'per_departure'] as $it)
                    <option value="{{ $it }}">{{ __('wizard.variant_inventory.'.$it) }}</option>
                @endforeach
            </select>
            @error('form.inventory_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'inventory_total',
                'label' => __('filament.resources.service_variant_fields.inventory_total'),
            ])
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
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'capacity_min',
                'label' => __('filament.resources.service_variant_fields.capacity_min'),
            ])
            <input type="number" min="0" class="form-control" wire:model.blur="form.capacity_min">
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'capacity_max',
                'label' => __('filament.resources.service_variant_fields.capacity_max'),
            ])
            <input type="number" min="0" class="form-control" wire:model.blur="form.capacity_max">
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'min_advance_booking_hours',
                'label' => __('filament.resources.service_variant_fields.min_advance_booking_hours'),
            ])
            <input type="number" min="0" class="form-control" wire:model.blur="form.min_advance_booking_hours">
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'max_advance_booking_days',
                'label' => __('filament.resources.service_variant_fields.max_advance_booking_days'),
            ])
            <input type="number" min="0" class="form-control" wire:model.blur="form.max_advance_booking_days">
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'start_time',
                'label' => __('filament.resources.service_variant_fields.start_time'),
            ])
            <input type="time" class="form-control @error('form.start_time') is-invalid @enderror" wire:model.blur="form.start_time">
            @error('form.start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'end_time',
                'label' => __('filament.resources.service_variant_fields.end_time'),
            ])
            <input type="time" class="form-control @error('form.end_time') is-invalid @enderror" wire:model.blur="form.end_time">
            @error('form.end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div @class(['d-none' => $variantFormTab !== 'pricing'])>
    <div class="row g-2">
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'pricing_type',
                'label' => __('filament.resources.service_variant_fields.pricing_type'),
            ])
            <select class="form-select @error('form.pricing_type') is-invalid @enderror" wire:model.live="form.pricing_type">
                @foreach (['per_person', 'per_unit', 'per_room', 'per_vehicle', 'per_group'] as $pt)
                    <option value="{{ $pt }}">{{ __('wizard.variant_pricing.'.$pt) }}</option>
                @endforeach
            </select>
            @error('form.pricing_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'base_price',
                'label' => __('filament.resources.service_variant_fields.base_price'),
            ])
            <input type="text" inputmode="decimal" class="form-control @error('form.base_price') is-invalid @enderror" wire:model.blur="form.base_price">
            @error('form.base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            @include('livewire.service-wizard.partials.variant-field-label', [
                'fieldKey' => 'currency_id',
                'label' => __('filament.resources.service_variant_fields.currency'),
            ])
            <select class="form-select @error('form.currency_id') is-invalid @enderror" wire:model.live="form.currency_id">
                <option value="">—</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->display_name }}</option>
                @endforeach
            </select>
            @error('form.currency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div @class(['d-none' => $variantFormTab !== 'descriptions'])>
    <p class="text-muted small mb-3">{{ __('wizard.variants_descriptions_help') }}</p>
    <div class="row g-3">
        @foreach ($languages as $language)
            <div class="col-12 col-md-6 col-lg-4" wire:key="variant-lang-{{ $language->id }}">
                <div class="border rounded p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                        <h6 class="mb-0">{{ $language->display_name }}</h6>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            title="{{ __('wizard.step6_translate') }}"
                            wire:click="translateDescriptions({{ $language->id }})"
                            wire:loading.attr="disabled"
                            wire:target="translateDescriptions({{ $language->id }})"
                        >
                            <span wire:loading.remove wire:target="translateDescriptions({{ $language->id }})">🌐</span>
                            <span wire:loading wire:target="translateDescriptions({{ $language->id }})" class="spinner-border spinner-border-sm" role="status"></span>
                            <span class="visually-hidden">{{ __('wizard.step6_translate') }}</span>
                        </button>
                    </div>
                    <div class="mb-3">
                        @include('livewire.service-wizard.partials.variant-field-label', [
                            'fieldKey' => 'name',
                            'label' => __('wizard.variants_translation_name'),
                            'required' => true,
                            'uniqueSuffix' => 'lang-'.$language->id,
                        ])
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
                        @include('livewire.service-wizard.partials.variant-field-label', [
                            'fieldKey' => 'description',
                            'label' => __('wizard.variants_translation_description'),
                            'uniqueSuffix' => 'lang-'.$language->id,
                        ])
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
</div>

<div @class(['d-none' => $variantFormTab !== 'images'])>
    <p class="text-muted small mb-3">{{ __('wizard.variant_media_help') }}</p>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-1 mb-2">
                <h6 class="small text-uppercase text-muted mb-0">{{ __('wizard.variant_media_main_heading') }}</h6>
                <x-catalog-helper-icon
                    :html="$catalogVariantFieldHelpHtml['main'] ?? null"
                    trigger-id="step4-variant-helper-main"
                    content-id="step4-variant-helper-main-html"
                    :aria-label="__('wizard.catalog_helper.aria_label_variant_field', ['field' => __('wizard.variant_media_main_heading')])"
                    wire:click.stop
                />
            </div>
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
            <div class="d-flex align-items-center gap-1 mb-2">
                <h6 class="small text-uppercase text-muted mb-0">{{ __('wizard.variant_media_gallery_heading') }}</h6>
                <x-catalog-helper-icon
                    :html="$catalogVariantFieldHelpHtml['gallery'] ?? null"
                    trigger-id="step4-variant-helper-gallery"
                    content-id="step4-variant-helper-gallery-html"
                    :aria-label="__('wizard.catalog_helper.aria_label_variant_field', ['field' => __('wizard.variant_media_gallery_heading')])"
                    wire:click.stop
                />
            </div>
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
</div>

<div class="mt-4 pt-2 border-top">
    <button type="button" class="btn btn-outline-secondary me-2" wire:click="requestCancel">
        {{ __('wizard.variants_back_to_list') }}
    </button>
    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="save">{{ __('wizard.variants_save') }}</span>
        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    </button>
</div>
