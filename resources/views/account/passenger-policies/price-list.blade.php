@extends('layouts.base', ['title' => __('account.passenger_policies.price_list_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <x-account-page-header
                :title="__('account.passenger_policies.price_list_heading', ['list' => $priceList->name])"
                :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                :instructions="__('account.passenger_policies.price_list_instructions')"
            />

            <form method="POST" action="{{ route('account.provider-price-lists.passenger-fares.update', $priceList) }}" class="card mt-4">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @forelse ($priceList->items as $item)
                        @php
                            $variant = $item->serviceVariant;
                            $bands = $variant?->service?->passengerPolicy?->rules ?? collect();
                        @endphp
                        <div class="border-top py-3">
                            <h2 class="h6">{{ $variant?->dropdownLabel() ?: ($variant?->sku ?: '#'.$item->id) }}</h2>
                            @if ($variant?->usesPaxBands())
                                <p class="text-muted mb-0">{{ __('account.passenger_policies.pax_list_note') }}</p>
                            @elseif ($bands->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.passenger_policies.service_without_policy') }}</p>
                            @else
                                @foreach ($bands as $band)
                                    @php
                                        $stored = $item->passengerPriceRules->firstWhere('provider_passenger_policy_rule_id', $band->id);
                                        $type = old('items.'.$item->id.'.categories.'.$band->passenger_category_id.'.adjustment_type', $stored?->adjustment_type?->value ?? $stored?->adjustment_type);
                                        $value = old('items.'.$item->id.'.categories.'.$band->passenger_category_id.'.adjustment_value', $stored?->adjustment_value ?? '');
                                    @endphp
                                    <div class="row align-items-end mb-2">
                                        <div class="col-lg-4">
                                            <div class="mb-2">
                                                <strong>{{ $band->category?->name }}</strong>
                                                <span class="text-muted">({{ $band->ageRangeLabel() }})</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="type-{{ $item->id }}-{{ $band->id }}">{{ __('account.passenger_policies.adjustment_type_label') }}</label>
                                            <select class="form-select" id="type-{{ $item->id }}-{{ $band->id }}" name="items[{{ $item->id }}][categories][{{ $band->passenger_category_id }}][adjustment_type]">
                                                <option value="">{{ __('account.passenger_policies.regular_price') }}</option>
                                                @foreach ($adjustmentOptions as $optionValue => $label)
                                                    <option value="{{ $optionValue }}" @selected((string) $type === (string) $optionValue)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label" for="value-{{ $item->id }}-{{ $band->id }}">{{ __('account.passenger_policies.adjustment_value') }}</label>
                                            <input type="number" min="0" step="0.01" class="form-control @error('items.'.$item->id.'.categories.'.$band->passenger_category_id.'.adjustment_value') is-invalid @enderror" id="value-{{ $item->id }}-{{ $band->id }}" name="items[{{ $item->id }}][categories][{{ $band->passenger_category_id }}][adjustment_value]" value="{{ $value }}">
                                            @error('items.'.$item->id.'.categories.'.$band->passenger_category_id.'.adjustment_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('account.passenger_policies.no_list_items') }}</p>
                    @endforelse
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-light" href="{{ route('account.provider-price-lists.edit', $priceList) }}">{{ __('account.passenger_policies.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('account.passenger_policies.save') }}</button>
                </div>
            </form>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
