@extends('layouts.base', ['title' => __('account.passenger_policies.service_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <x-account-page-header
                :title="__('account.passenger_policies.service_heading', ['service' => $service->name !== '' ? $service->name : '#'.$service->id])"
                :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                :instructions="__('account.passenger_policies.service_instructions')"
            />

            @php
                $policyRules = $service->passengerPolicy?->rules ?? collect();
            @endphp

            @if ($service->serviceVariants->isEmpty())
                <div class="alert alert-info mt-4" role="alert">{{ __('account.passenger_policies.no_variants') }}</div>
                <div class="d-flex justify-content-end mt-3">
                    <a class="btn btn-light" href="{{ route('catalog') }}">{{ __('account.passenger_policies.back') }}</a>
                </div>
            @else
                <form method="POST" action="{{ route('account.services.passenger-policy.update', $service) }}" class="card mt-4">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($service->passengerPolicy)
                            <h2 class="h5">{{ $service->passengerPolicy->name }}</h2>
                            <p class="text-muted">{{ __('account.passenger_policies.service_policy_help') }}</p>
                            <ul class="mb-4">
                                @foreach ($policyRules as $policyRule)
                                    <li>{{ $policyRule->category?->name ?? '#'.$policyRule->passenger_category_id }}: {{ $policyRule->ageRangeLabel() }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">{{ __('account.passenger_policies.pax_no_policy_help') }}</p>
                        @endif

                        <h2 class="h5">{{ __('account.passenger_policies.pax_heading') }}</h2>
                        <p class="text-muted">{{ __('account.passenger_policies.pax_help') }}</p>

                        @foreach ($service->serviceVariants as $variant)
                            @php
                                $rows = $paxBands[$variant->id] ?? [];
                                $hasBands = $rows !== [];
                            @endphp
                            @php
                                $meta = $variantMeta[$variant->id] ?? ['base_price' => '—', 'currency_code' => '', 'capacity' => null];
                            @endphp
                            <div class="border-top py-3">
                                <div class="d-flex justify-content-between align-items-center gap-3 mb-1">
                                    <h3 class="h6 mb-0">{{ $variant->dropdownLabel(false) }}</h3>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-pax-add="{{ $variant->id }}">{{ __('account.passenger_policies.pax_add') }}</button>
                                </div>
                                <p class="small text-muted mb-2">
                                    {{ __('account.passenger_policies.base_price', ['price' => $meta['base_price']]) }}
                                    @if ($meta['capacity'])
                                        <span class="ms-2">{{ $meta['capacity'] }}</span>
                                    @endif
                                </p>
                                <input type="hidden" name="pax_bands[{{ $variant->id }}][_present]" value="1">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-2">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.passenger_policies.pax_min') }}</th>
                                                <th>{{ __('account.passenger_policies.pax_max') }}</th>
                                                <th>
                                                    {{ __('account.passenger_policies.pax_price') }}
                                                    @if ($meta['currency_code'] !== '')
                                                        ({{ $meta['currency_code'] }})
                                                    @endif
                                                </th>
                                                @foreach ($policyRules as $policyRule)
                                                    <th>{{ $policyRule->category?->name ?? '#'.$policyRule->passenger_category_id }}</th>
                                                @endforeach
                                                <th class="text-end">{{ __('account.passenger_policies.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pax-band-rows-{{ $variant->id }}" data-variant="{{ $variant->id }}">
                                            @foreach ($rows as $index => $row)
                                                @include('account.passenger-policies.partials.pax-band-row', [
                                                    'variant' => $variant,
                                                    'index' => $index,
                                                    'row' => $row,
                                                    'policyRules' => $policyRules,
                                                ])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="small text-muted mb-0 {{ $hasBands ? 'd-none' : '' }}" data-pax-empty="{{ $variant->id }}">{{ __('account.passenger_policies.pax_empty') }}</p>

                                <template id="pax-band-template-{{ $variant->id }}">
                                    @include('account.passenger-policies.partials.pax-band-row', [
                                        'variant' => $variant,
                                        'index' => '__INDEX__',
                                        'row' => ['min_pax' => '', 'max_pax' => '', 'price' => '', 'categories' => []],
                                        'policyRules' => $policyRules,
                                    ])
                                </template>

                                @if ($service->passengerPolicy)
                                    <div id="age-adjustments-{{ $variant->id }}" class="mt-3 {{ $hasBands ? 'd-none' : '' }}">
                                        <h4 class="h6">{{ __('account.passenger_policies.prices_heading') }}</h4>
                                        <p class="small text-muted">{{ __('account.passenger_policies.pax_adjustments_help') }}</p>
                                        @foreach ($policyRules as $policyRule)
                                            @php
                                                $category = $policyRule->category;
                                                $price = $variantPrices[$variant->id][$policyRule->passenger_category_id] ?? ['adjustment_type' => '', 'adjustment_value' => ''];
                                            @endphp
                                            <div class="row align-items-end mb-2">
                                                <div class="col-lg-4">
                                                    <div class="form-label mb-2">{{ $category?->name ?? '#'.$policyRule->passenger_category_id }}</div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label class="form-label" for="type-{{ $variant->id }}-{{ $policyRule->passenger_category_id }}">{{ __('account.passenger_policies.adjustment_type_label') }}</label>
                                                    <select
                                                        class="form-select @error('variant_prices.'.$variant->id.'.'.$policyRule->passenger_category_id.'.adjustment_type') is-invalid @enderror"
                                                        id="type-{{ $variant->id }}-{{ $policyRule->passenger_category_id }}"
                                                        name="variant_prices[{{ $variant->id }}][{{ $policyRule->passenger_category_id }}][adjustment_type]"
                                                        @disabled($hasBands)
                                                    >
                                                        <option value="">{{ __('account.passenger_policies.regular_price') }}</option>
                                                        @foreach ($adjustmentOptions as $value => $label)
                                                            <option value="{{ $value }}" @selected((string) ($price['adjustment_type'] ?? '') === (string) $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label class="form-label" for="value-{{ $variant->id }}-{{ $policyRule->passenger_category_id }}">{{ __('account.passenger_policies.adjustment_value') }}</label>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        class="form-control @error('variant_prices.'.$variant->id.'.'.$policyRule->passenger_category_id.'.adjustment_value') is-invalid @enderror"
                                                        id="value-{{ $variant->id }}-{{ $policyRule->passenger_category_id }}"
                                                        name="variant_prices[{{ $variant->id }}][{{ $policyRule->passenger_category_id }}][adjustment_value]"
                                                        value="{{ $price['adjustment_value'] ?? '' }}"
                                                        @disabled($hasBands)
                                                    >
                                                    @error('variant_prices.'.$variant->id.'.'.$policyRule->passenger_category_id.'.adjustment_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a class="btn btn-light" href="{{ route('catalog') }}">{{ __('account.passenger_policies.back') }}</a>
                        <a class="btn btn-light" href="{{ route('catalog') }}">{{ __('account.passenger_policies.cancel') }}</a>
                        @if ($service->passengerPolicy)
                            <button type="submit" class="btn btn-outline-danger" form="remove-passenger-policy">{{ __('account.passenger_policies.remove') }}</button>
                        @endif
                        <button type="submit" class="btn btn-primary">{{ __('account.passenger_policies.save') }}</button>
                    </div>
                </form>

                @if ($service->passengerPolicy)
                    <form id="remove-passenger-policy" method="POST" action="{{ route('account.services.passenger-policy.destroy', $service) }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            @endif
        </div>
    </section>

    <x-site-footer-simple />

    <script>
        document.querySelectorAll('[data-pax-add]').forEach(function (button) {
            button.addEventListener('click', function () {
                var variantId = button.getAttribute('data-pax-add');
                var template = document.getElementById('pax-band-template-' + variantId);
                var body = document.getElementById('pax-band-rows-' + variantId);
                if (!template || !body) {
                    return;
                }

                body.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(Date.now())));
                syncPaxAdjustments(variantId);
            });
        });

        document.addEventListener('click', function (event) {
            var remove = event.target.closest('[data-pax-remove]');
            if (!remove) {
                return;
            }

            var row = remove.closest('[data-pax-row]');
            var body = row ? row.parentElement : null;
            if (!row || !body) {
                return;
            }

            var variantId = body.getAttribute('data-variant');
            row.remove();
            syncPaxAdjustments(variantId);
        });

        function syncPaxAdjustments(variantId) {
            var body = document.getElementById('pax-band-rows-' + variantId);
            var box = document.getElementById('age-adjustments-' + variantId);
            if (!body) {
                return;
            }

            var hasRows = body.querySelector('[data-pax-row]') !== null;
            var empty = document.querySelector('[data-pax-empty="' + variantId + '"]');
            if (empty) {
                empty.classList.toggle('d-none', hasRows);
            }
            if (!box) {
                return;
            }
            box.classList.toggle('d-none', hasRows);
            box.querySelectorAll('input, select').forEach(function (field) {
                field.disabled = hasRows;
            });
        }
    </script>

@endsection
