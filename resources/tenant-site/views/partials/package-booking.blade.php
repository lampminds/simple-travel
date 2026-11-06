@php
    /** @var array<string, mixed> $package */
    $booking = $package['booking'] ?? [];
    $canBook = (bool) ($booking['can_book'] ?? false);
    $inputClass = 'w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0';
@endphp

@if ($canBook)
    <form method="POST" action="{{ route('account.reservations.store') }}" class="mt-6 pt-6 border-t border-slate-100 dark:border-gray-800">
        @csrf
        <input type="hidden" name="package_offer_uuid" value="{{ $booking['offer_uuid'] }}">

        <h5 class="text-lg font-semibold mb-1">{{ __('tenant.package_book_heading') }}</h5>
        <p class="text-slate-400 text-sm mb-4">{{ __('tenant.package_book_hint') }}</p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 text-red-600 text-sm p-3 dark:bg-red-500/10" role="alert">
                <ul class="list-disc ps-4 mb-0 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $ageValues = old('ages', ['']);
            if (! is_array($ageValues) || $ageValues === []) {
                $ageValues = [''];
            }
        @endphp

        <p class="text-sm font-medium mb-2">{{ __('tenant.package_book_passengers') }}</p>
        <p class="text-slate-400 text-xs mb-3">{{ __('tenant.package_book_passengers_hint') }}</p>

        <div id="tenant-passenger-ages" class="space-y-3 mb-3">
            @foreach ($ageValues as $index => $ageValue)
                <div class="tenant-passenger-age flex items-end gap-2">
                    <div class="flex-1">
                        <label class="form-label text-sm text-slate-400" @if ($index === 0) for="tenant_passenger_age_0" @endif>
                            {{ __('account.reservations.field_age') }}
                        </label>
                        <input
                            type="number"
                            name="ages[]"
                            @if ($index === 0) id="tenant_passenger_age_0" @endif
                            class="{{ $inputClass }} mt-1"
                            value="{{ $ageValue }}"
                            min="0"
                            max="120"
                            required
                        >
                    </div>
                    <button type="button" class="tenant-remove-passenger-age py-2 px-3 text-sm border border-slate-200 rounded-md">
                        {{ __('account.reservations.remove_passenger') }}
                    </button>
                </div>
            @endforeach
        </div>
        <button type="button" id="tenant-add-passenger-age" class="mb-4 py-2 px-3 text-sm border border-slate-200 rounded-md">
            {{ __('account.reservations.add_passenger') }}
        </button>
        <template id="tenant-passenger-age-template">
            <div class="tenant-passenger-age flex items-end gap-2">
                <div class="flex-1">
                    <label class="form-label text-sm text-slate-400">{{ __('account.reservations.field_age') }}</label>
                    <input type="number" name="ages[]" class="{{ $inputClass }} mt-1" min="0" max="120" required>
                </div>
                <button type="button" class="tenant-remove-passenger-age py-2 px-3 text-sm border border-slate-200 rounded-md">
                    {{ __('account.reservations.remove_passenger') }}
                </button>
            </div>
        </template>
        <script>
            (function () {
                var list = document.getElementById('tenant-passenger-ages');
                var addButton = document.getElementById('tenant-add-passenger-age');
                var template = document.getElementById('tenant-passenger-age-template');
                if (!list || !addButton || !template) {
                    return;
                }
                addButton.addEventListener('click', function () {
                    if (list.querySelectorAll('.tenant-passenger-age').length >= 30) {
                        return;
                    }
                    list.appendChild(template.content.cloneNode(true));
                });
                list.addEventListener('click', function (event) {
                    var button = event.target.closest('.tenant-remove-passenger-age');
                    if (!button) {
                        return;
                    }
                    if (list.querySelectorAll('.tenant-passenger-age').length < 2) {
                        return;
                    }
                    button.closest('.tenant-passenger-age').remove();
                });
            })();
        </script>

        <div class="grid grid-cols-1 gap-3 mb-4">
            <div>
                <label for="travel_start_date" class="form-label text-sm text-slate-400">{{ __('account.reservations.field_travel_start') }}</label>
                <input
                    type="date"
                    name="travel_start_date"
                    id="travel_start_date"
                    class="{{ $inputClass }} mt-1"
                    value="{{ old('travel_start_date') }}"
                    required
                >
            </div>
            <div>
                <label for="travel_end_date" class="form-label text-sm text-slate-400">{{ __('account.reservations.field_travel_end') }}</label>
                <input
                    type="date"
                    name="travel_end_date"
                    id="travel_end_date"
                    class="{{ $inputClass }} mt-1"
                    value="{{ old('travel_end_date') }}"
                    required
                >
            </div>
            <div>
                <label for="remarks_customer" class="form-label text-sm text-slate-400">{{ __('account.reservations.field_remarks') }}</label>
                <textarea
                    name="remarks_customer"
                    id="remarks_customer"
                    rows="3"
                    class="w-full py-2 px-3 mt-1 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0"
                >{{ old('remarks_customer') }}</textarea>
            </div>
        </div>

        <button type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full border-0 cursor-pointer">
            {{ __('tenant.package_book_submit') }}
        </button>
    </form>
@elseif ($booking['show_status'] ?? false)
    <p class="text-slate-400 text-sm mt-4 mb-0">
        {{ ($booking['pending'] ?? false) ? __('tenant.package_book_pending') : __('tenant.package_book_unavailable') }}
    </p>
    @if ($booking['pending'] ?? false)
        <a href="{{ route('account.package-offers.index', ['as' => 'agency']) }}" class="mt-3 py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">
            {{ __('tenant.package_book_pending_action') }}
        </a>
    @endif
@endif
