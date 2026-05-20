@extends('layouts.base', ['title' => __('exchange_rates.page_title')])

@section('css')
    <style>
        .exchange-rates-compact > :not(caption) > * > * {
            padding: 0.35rem 0.65rem !important;
        }
        .exchange-rates-compact {
            width: auto;
            max-width: 100%;
        }
        .exchange-rates-panel {
            width: fit-content;
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    @php
        $formatRate = static function ($value): string {
            if ($value === null) {
                return '—';
            }

            return rtrim(rtrim(number_format((float) $value, 4, ',', ' '), '0'), ',') ?: '0';
        };
    @endphp

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('exchange_rates.page_title') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ $canEdit ? __('exchange_rates.intro') : __('exchange_rates.intro_readonly') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card mt-3 border-0 shadow-sm exchange-rates-panel">
                <div class="card-body py-3">
                    <form method="get" action="{{ route('account.exchange-rates.index') }}" class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label for="rate-date-filter" class="form-label">{{ __('exchange_rates.rate_date') }}</label>
                            <input
                                type="date"
                                id="rate-date-filter"
                                name="date"
                                class="form-control"
                                value="{{ $rateDayInput }}"
                                required
                            >
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary">{{ __('exchange_rates.filter_date') }}</button>
                        </div>
                        @if ($canEdit)
                            <div class="col-auto">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <a
                                    href="{{ route('account.exchange-rates.edit', ['date' => $rateDayInput, 'from_system' => 1]) }}"
                                    class="btn btn-primary"
                                >{{ __('exchange_rates.preload_button') }}</a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row flex-wrap gap-3 mt-3 align-items-start">
                <div class="card border-0 shadow-sm exchange-rates-panel">
                    <div class="card-header bg-transparent border-bottom py-2">
                        <h2 class="h6 mb-0 fw-semibold">
                            {{ __('exchange_rates.system_heading') }}
                            <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">{{ __('exchange_rates.system_badge') }}</span>
                        </h2>
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-sm table-hover align-middle mb-0 exchange-rates-compact">
                            <thead>
                                <tr>
                                    <th class="pe-3">{{ __('exchange_rates.columns.currency') }}</th>
                                    <th class="text-end pe-3">{{ __('exchange_rates.columns.buy') }}</th>
                                    <th class="text-end">{{ __('exchange_rates.columns.sell') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($systemRows as $row)
                                    <tr>
                                        <td class="fw-medium pe-3">{{ $row['code'] }}</td>
                                        <td class="text-end font-monospace pe-3">
                                            @if ($row['buy'] !== null)
                                                {{ $formatRate($row['buy']) }}
                                            @else
                                                <span class="text-muted small">{{ __('exchange_rates.system_empty') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end font-monospace">
                                            {{ $row['sell'] !== null ? $formatRate($row['sell']) : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm exchange-rates-panel">
                    <div class="card-header bg-transparent border-bottom py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h6 mb-0 fw-semibold">
                            {{ __('exchange_rates.tenant_heading', ['date' => $rateDay->translatedFormat('d M Y')]) }}
                        </h2>
                        @if ($canEdit)
                            <a
                                href="{{ route('account.exchange-rates.edit', ['date' => $rateDayInput]) }}"
                                class="btn btn-sm btn-outline-primary"
                            >{{ __('exchange_rates.edit_button') }}</a>
                        @endif
                    </div>
                    @if ($tenantHasRates)
                        <div class="card-body py-2">
                            <table class="table table-sm table-hover align-middle mb-0 exchange-rates-compact">
                                <thead>
                                    <tr>
                                        <th class="pe-3">{{ __('exchange_rates.columns.currency') }}</th>
                                        <th class="text-end pe-3">{{ __('exchange_rates.columns.buy') }}</th>
                                        <th class="text-end pe-3">{{ __('exchange_rates.columns.sell') }}</th>
                                        <th class="text-center">{{ __('exchange_rates.columns.active') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenantRows as $row)
                                        <tr class="{{ ! $row['is_active'] ? 'text-muted' : '' }}">
                                            <td class="fw-medium pe-3">{{ $row['code'] }}</td>
                                            <td class="text-end font-monospace pe-3">
                                                {{ $row['buy'] !== null ? $formatRate($row['buy']) : '—' }}
                                            </td>
                                            <td class="text-end font-monospace pe-3">
                                                {{ $row['sell'] !== null ? $formatRate($row['sell']) : '—' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $row['is_active'] ? __('exchange_rates.active_yes') : __('exchange_rates.active_no') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body py-2 text-muted small">
                            {{ __('exchange_rates.tenant_empty') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3 border-0 shadow-sm exchange-rates-panel">
                <div class="card-header bg-transparent border-bottom py-2">
                    <h2 class="h6 mb-0 fw-semibold">{{ __('exchange_rates.history_heading') }}</h2>
                </div>
                @if ($historyDays === [])
                    <div class="card-body py-2 text-muted small">{{ __('exchange_rates.history_empty') }}</div>
                @else
                    <div class="card-body py-2">
                        <table class="table table-sm table-hover align-middle mb-0 exchange-rates-compact">
                            <thead>
                                <tr>
                                    <th class="pe-3">{{ __('exchange_rates.columns.day') }}</th>
                                    <th class="pe-3">{{ __('exchange_rates.columns.summary') }}</th>
                                    <th class="pe-3">{{ __('exchange_rates.columns.active') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historyDays as $day)
                                    <tr>
                                        <td class="fw-medium pe-3">{{ $day['label'] }}</td>
                                        <td class="text-muted pe-3">{{ $day['total'] }}</td>
                                        <td class="pe-3">{{ $day['active_count'] }} / {{ $day['total'] }}</td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('account.exchange-rates.index', ['date' => $day['date']]) }}" class="btn btn-sm btn-link px-1 py-0">
                                                {{ __('exchange_rates.history_view') }}
                                            </a>
                                            @if ($canEdit)
                                                <a href="{{ route('account.exchange-rates.edit', ['date' => $day['date']]) }}" class="btn btn-sm btn-link px-1 py-0">
                                                    {{ __('exchange_rates.history_edit') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
