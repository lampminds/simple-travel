<div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required-label">{{ __('wizard.step2_fields.status') }}</label>
            <select class="form-select @error('form.status') is-invalid @enderror" wire:model.live="form.status">
                @foreach (['active', 'onhold', 'suspended', 'discontinued', 'inactive', 'terminated'] as $status)
                    <option value="{{ $status }}">{{ __('wizard.step2_status.'.$status) }}</option>
                @endforeach
            </select>
            @error('form.status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('wizard.step2_fields.duration_minutes') }}</label>
            <input type="number" min="0" class="form-control @error('form.duration_minutes') is-invalid @enderror" wire:model.blur="form.duration_minutes">
            @error('form.duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('wizard.step2_fields.booking_mode') }}</label>
            <select class="form-select @error('form.booking_mode') is-invalid @enderror" wire:model.live="form.booking_mode">
                <option value="">{{ __('wizard.step2_booking_mode.none') }}</option>
                @foreach (['instant', 'request', 'external', 'quote'] as $mode)
                    <option value="{{ $mode }}">{{ __('wizard.step2_booking_mode.'.$mode) }}</option>
                @endforeach
            </select>
            @error('form.booking_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('wizard.step2_fields.confirmation_time_hours') }}</label>
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
                <div class="col-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is-bookable" wire:model.live="form.is_bookable">
                        <label class="form-check-label" for="is-bookable">{{ __('wizard.step2_fields.is_bookable') }}</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is-featured" wire:model.live="form.is_featured">
                        <label class="form-check-label" for="is-featured">{{ __('wizard.step2_fields.is_featured') }}</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is-public" wire:model.live="form.is_public">
                        <label class="form-check-label" for="is-public">{{ __('wizard.step2_fields.is_public') }}</label>
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
