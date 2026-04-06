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
                            <th scope="col">{{ __('wizard.variants_col_sku') }}</th>
                            <th scope="col">{{ __('wizard.variants_col_status') }}</th>
                            <th scope="col">{{ __('wizard.variants_col_price') }}</th>
                            <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $variant)
                            <tr>
                                <td class="fw-medium">{{ $variant->sku }}</td>
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

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('filament.resources.service_variant_fields.sku') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('form.sku') is-invalid @enderror" wire:model.blur="form.sku">
                        @error('form.sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('filament.resources.service_variant_fields.status') }}</label>
                        <select class="form-select @error('form.status') is-invalid @enderror" wire:model.live="form.status">
                            @foreach (['active', 'inactive', 'hidden'] as $st)
                                <option value="{{ $st }}">{{ __('filament.resources.service_variant_status.'.$st) }}</option>
                            @endforeach
                        </select>
                        @error('form.status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
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

                <div class="mt-4">
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ __('wizard.variants_save') }}</span>
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
