@extends('layouts.base', ['title' => __('account.provider_supply.offers_page_title', ['company' => $counterpartLabel])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">{{ $errors->first() }}</div>
            @endif

            <x-account-page-header
                :title="__('account.provider_supply.offers_heading')"
                :subtitle="$counterpartLabel"
                :instructions="__('account.provider_supply.offers_intro')"
            />

            <form method="post" action="{{ route('account.provider-supply.offers.update', $counterpart) }}" class="mt-4">
                @csrf
                @method('PUT')

                @forelse ($services as $service)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h2 class="h6 fw-semibold mb-3">{{ $service->name !== '' ? $service->name : ('#'.$service->id) }}</h2>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('account.provider_supply.col_offer') }}</th>
                                            <th>{{ __('account.provider_supply.col_variant') }}</th>
                                            <th>{{ __('account.provider_supply.col_offer_status') }}</th>
                                            <th>{{ __('account.provider_supply.col_availability') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($service->serviceVariants as $variant)
                                            @php
                                                $status = (string) $variant->getAttribute('supply_offer_status');
                                                $accepted = $status === 'accepted';
                                            @endphp
                                            <tr>
                                                <td style="width: 4rem;">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="propose[]"
                                                        value="{{ $variant->id }}"
                                                        @checked(in_array($status, ['pending', 'accepted'], true))
                                                        @disabled($accepted)
                                                    >
                                                    @if ($accepted)
                                                        <input type="hidden" name="propose[]" value="{{ $variant->id }}">
                                                    @endif
                                                </td>
                                                <td>{{ $variant->dropdownLabel(false) }}</td>
                                                <td>{{ __('account.provider_supply.offer_status.'.$status) }}</td>
                                                <td>
                                                    @if ($accepted)
                                                        <select name="availability[{{ $variant->id }}]" class="form-select form-select-sm">
                                                            @foreach (['active', 'suspended', 'discontinued'] as $availability)
                                                                <option value="{{ $availability }}" @selected($variant->getAttribute('supply_offer_availability') === $availability)>
                                                                    {{ __('account.provider_supply.availability.'.$availability) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border">{{ __('account.provider_supply.offers_empty') }}</div>
                @endforelse

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('account.relationships.index') }}" class="btn btn-outline-secondary">{{ __('account.provider_supply.back') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('account.provider_supply.save') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
