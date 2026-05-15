<div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold">{{ __('wizard.step7_hotel_validation_heading') }}</div>
            <ul class="mb-0 mt-2 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($saveMessage)
        <div class="alert alert-success py-2 small" role="status">{{ $saveMessage }}</div>
    @endif

    @if ($categories->isEmpty())
        <div class="alert alert-warning mb-0" role="alert">
            {{ __('wizard.step7_hotel_no_catalog') }}
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('wizard.step7_hotel_intro') }}</p>

        <div class="row g-3">
            <div class="col-12 col-lg-10">
                <fieldset class="border rounded-2 px-3 py-2 @error('hotelTypeIds') border-danger @enderror">
                    <legend class="float-none w-auto px-1 fs-6 mb-2">
                        {{ __('wizard.step7_hotel_field_types') }}
                    </legend>
                    <p class="text-muted small mb-2 mb-lg-3">{{ __('wizard.step7_hotel_types_help') }}</p>
                    @foreach ($categories as $category)
                        <div class="mb-3 @if ($loop->last) mb-0 @endif">
                            <div class="fw-semibold small text-secondary mb-2">
                                {{ $category->name !== '' ? $category->name : $category->code }}
                            </div>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-2">
                                @foreach ($category->serviceHotelTypes as $type)
                                    <div class="col">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input @error('hotelTypeIds') is-invalid @enderror"
                                                type="checkbox"
                                                id="hotel-type-{{ (int) $type->id }}"
                                                value="{{ (int) $type->id }}"
                                                wire:model="hotelTypeIds"
                                            >
                                            <label class="form-check-label" for="hotel-type-{{ (int) $type->id }}">
                                                {{ $type->name !== '' ? $type->name : $type->code }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </fieldset>
                @error('hotelTypeIds')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('hotelTypeIds.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label" for="hotel-stars">{{ __('wizard.step7_hotel_field_stars') }}</label>
                <select id="hotel-stars" class="form-select @error('stars') is-invalid @enderror" wire:model="stars">
                    <option value="">{{ __('wizard.step7_hotel_stars_unset') }}</option>
                    @for ($s = 1; $s <= 5; $s++)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endfor
                </select>
                @error('stars')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="hotel-check-in">{{ __('wizard.step7_hotel_field_check_in') }}</label>
                <input
                    id="hotel-check-in"
                    type="time"
                    class="form-control @error('checkInTime') is-invalid @enderror"
                    wire:model="checkInTime"
                >
                @error('checkInTime')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="hotel-check-out">{{ __('wizard.step7_hotel_field_check_out') }}</label>
                <input
                    id="hotel-check-out"
                    type="time"
                    class="form-control @error('checkOutTime') is-invalid @enderror"
                    wire:model="checkOutTime"
                >
                @error('checkOutTime')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="hotel-rooms">{{ __('wizard.step7_hotel_field_rooms') }}</label>
                <input
                    id="hotel-rooms"
                    type="number"
                    class="form-control @error('roomsCount') is-invalid @enderror"
                    wire:model="roomsCount"
                    min="0"
                    step="1"
                >
                @error('roomsCount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-lg-8">
                <label class="form-label" for="hotel-chain">{{ __('wizard.step7_hotel_field_chain') }}</label>
                <input
                    id="hotel-chain"
                    type="text"
                    class="form-control @error('chainName') is-invalid @enderror"
                    wire:model.live="chainName"
                    maxlength="255"
                >
                @error('chainName')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button
                type="button"
                class="btn btn-primary"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">{{ __('wizard.step7_hotel_save') }}</span>
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status"></span>
            </button>
        </div>
    @endif
</div>
