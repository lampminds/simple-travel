<div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required-label">{{ __('wizard.step2_fields.status') }}</label>
            <select class="form-select @error('form.status') is-invalid @enderror" wire:model.live="form.status">
                @foreach (['active', 'onhold', 'suspended', 'discontinued', 'inactive', 'terminated'] as $status)
                    @if ($status !== 'active' || ($canActivateService ?? false))
                        <option value="{{ $status }}">{{ __('wizard.step2_status.'.$status) }}</option>
                    @endif
                @endforeach
            </select>
            @if (! ($canActivateService ?? false))
                <div class="form-text">{{ __('wizard.step2_active_requires_variants') }}</div>
            @endif
            @error('form.status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label required-label">{{ __('wizard.step2_fields.booking_mode') }}</label>
            <select class="form-select @error('form.booking_mode') is-invalid @enderror" wire:model.live="form.booking_mode">
                @foreach (['instant', 'request', 'external', 'quote'] as $mode)
                    <option value="{{ $mode }}">{{ __('wizard.step2_booking_mode.'.$mode) }}</option>
                @endforeach
            </select>
            @error('form.booking_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label d-inline-flex align-items-center gap-1">
                {{ __('wizard.step2_fields.confirmation_time_hours') }}
                <x-catalog-helper-icon
                    :html="$catalogConfirmationTimeHoursHelpHtml"
                    trigger-id="step2-catalog-helper-confirmation-time-hours"
                    content-id="step2-catalog-helper-confirmation-time-hours-html"
                    :aria-label="__('wizard.catalog_helper.aria_label_step2_field', ['field' => __('wizard.step2_fields.confirmation_time_hours')])"
                />
            </label>
            <input
                type="number"
                min="0"
                class="form-control @error('form.confirmation_time_hours') is-invalid @enderror"
                wire:model.blur="form.confirmation_time_hours"
            >
            @error('form.confirmation_time_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="is-featured" wire:model.live="form.is_featured">
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <label class="form-check-label mb-0" for="is-featured">{{ __('wizard.step2_fields.is_featured') }}</label>
                            <x-catalog-helper-icon
                                :html="$catalogFeaturedHelpHtml"
                                trigger-id="step2-catalog-helper-is-featured"
                                content-id="step2-catalog-helper-is-featured-html"
                                :aria-label="__('wizard.catalog_helper.aria_label_step2_field', ['field' => __('wizard.step2_fields.is_featured')])"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="is-public" wire:model.live="form.is_public">
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <label class="form-check-label mb-0" for="is-public">{{ __('wizard.step2_fields.is_public') }}</label>
                            <x-catalog-helper-icon
                                :html="$catalogPublicHelpHtml"
                                trigger-id="step2-catalog-helper-is-public"
                                content-id="step2-catalog-helper-is-public-html"
                                :aria-label="__('wizard.catalog_helper.aria_label_step2_field', ['field' => __('wizard.step2_fields.is_public')])"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">{{ __('wizard.step2_save') }}</span>
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </button>
    </div>
</div>
