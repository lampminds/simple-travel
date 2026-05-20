@extends('layouts.base', ['title' => __('exchange_rates.page_title_edit')])

@section('css')
    <style>
        .exchange-rates-compact > :not(caption) > * > * {
            padding: 0.35rem 0.5rem !important;
        }
        .exchange-rates-compact {
            width: auto;
            max-width: 100%;
        }
        .exchange-rates-edit-panel {
            width: fit-content;
            max-width: 100%;
        }
        .exchange-rates-edit-panel .form-control {
            max-width: 9rem;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('exchange_rates.page_title_edit') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ __('exchange_rates.edit_intro') }}
                        </p>
                        <p class="mt-1 small text-muted mb-0">
                            @if ($fromSystem)
                                {{ __('exchange_rates.edit_from_system', ['date' => $rateDay->translatedFormat('d M Y')]) }}
                            @else
                                {{ __('exchange_rates.edit_existing', ['date' => $rateDay->translatedFormat('d M Y')]) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="card mt-3 border-0 shadow-sm exchange-rates-edit-panel">
                <div class="card-body py-3">
                    <form method="post" action="{{ route('account.exchange-rates.store') }}">
                        @csrf
                        <input type="hidden" name="rate_date" value="{{ $rateDayInput }}">

                        <x-form-validation-summary />

                        <table class="table table-sm align-middle mb-0 exchange-rates-compact">
                                <thead>
                                    <tr>
                                        <th class="pe-3">{{ __('exchange_rates.columns.currency') }}</th>
                                        <th class="pe-3">{{ __('exchange_rates.columns.buy') }}</th>
                                        <th class="pe-3">{{ __('exchange_rates.columns.sell') }}</th>
                                        <th class="text-center">{{ __('exchange_rates.columns.active') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($formRows as $index => $row)
                                        <tr>
                                            <td class="pe-3">
                                                <input type="hidden" name="rates[{{ $index }}][currency_id]" value="{{ $row['currency_id'] }}">
                                                <span class="fw-medium">{{ $row['code'] }}</span>
                                            </td>
                                            <td class="pe-3">
                                                @if ($row['buy_disabled'])
                                                    <input type="hidden" name="rates[{{ $index }}][units_per_usd_buy]" value="1">
                                                    <span class="form-control-plaintext font-monospace py-1">1</span>
                                                @else
                                                    <input
                                                        type="number"
                                                        name="rates[{{ $index }}][units_per_usd_buy]"
                                                        class="form-control form-control-sm font-monospace @error('rates.'.$index.'.units_per_usd_buy') is-invalid @enderror"
                                                        value="{{ old('rates.'.$index.'.units_per_usd_buy', $row['buy']) }}"
                                                        step="0.00000001"
                                                        min="0.00000001"
                                                        required
                                                    >
                                                    <x-form-field-error :name="'rates.'.$index.'.units_per_usd_buy'" />
                                                @endif
                                            </td>
                                            <td class="pe-3">
                                                @if ($row['sell_disabled'])
                                                    <input type="hidden" name="rates[{{ $index }}][units_per_usd_sell]" value="1">
                                                    <span class="form-control-plaintext font-monospace py-1">1</span>
                                                @else
                                                    <input
                                                        type="number"
                                                        name="rates[{{ $index }}][units_per_usd_sell]"
                                                        class="form-control form-control-sm font-monospace @error('rates.'.$index.'.units_per_usd_sell') is-invalid @enderror"
                                                        value="{{ old('rates.'.$index.'.units_per_usd_sell', $row['sell']) }}"
                                                        step="0.00000001"
                                                        min="0.00000001"
                                                        required
                                                    >
                                                    <x-form-field-error :name="'rates.'.$index.'.units_per_usd_sell'" />
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" name="rates[{{ $index }}][is_active]" value="0">
                                                <input
                                                    type="checkbox"
                                                    name="rates[{{ $index }}][is_active]"
                                                    value="1"
                                                    class="form-check-input"
                                                    @checked(old('rates.'.$index.'.is_active', $row['is_active']))
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                        </table>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('exchange_rates.save_button') }}</button>
                            <a href="{{ route('account.exchange-rates.index', ['date' => $rateDayInput]) }}" class="btn btn-light">
                                {{ __('exchange_rates.cancel_button') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
