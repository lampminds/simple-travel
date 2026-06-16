@extends('layouts.base', ['title' => __('account.operator_package_services.page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.operator_package_services.heading')"
                            :instructions="__('account.operator_package_services.intro')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.operator-packages.index') }}" class="btn btn-outline-primary">
                                {{ __('account.operator_package_services.manage_packages_link') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <form method="get" action="{{ route('account.operator-package-services.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
                        <div>
                            <label for="service_offer" class="form-label small mb-1">{{ __('account.operator_package_services.filter_label') }}</label>
                            <select name="service_offer" id="service_offer" class="form-select form-select-sm" style="min-width: 22rem;" onchange="this.form.submit()">
                                <option value="all" @selected($serviceOfferFilter === null)>{{ __('account.operator_package_services.filter_all') }}</option>
                                @foreach ($serviceFilterOptions as $offerUuid => $label)
                                    <option value="{{ $offerUuid }}" @selected($serviceOfferFilter === $offerUuid)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($serviceFilterOptions === [])
                                <p class="text-muted mb-0">{{ __('account.operator_package_services.empty_no_services') }}</p>
                            @elseif ($packages->isEmpty())
                                <p class="text-muted mb-0">
                                    @if ($serviceOfferFilter !== null)
                                        {{ __('account.operator_package_services.empty_filtered', ['service' => $selectedServiceLabel]) }}
                                    @else
                                        {{ __('account.operator_package_services.empty_all') }}
                                    @endif
                                </p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.operator_package_services.col_package') }}</th>
                                                <th>{{ __('account.operator_package_services.col_status') }}</th>
                                                @if ($serviceOfferFilter !== null)
                                                    <th>{{ __('account.operator_package_services.col_day') }}</th>
                                                    <th>{{ __('account.operator_package_services.col_quantity') }}</th>
                                                    <th>{{ __('account.operator_package_services.col_inclusion') }}</th>
                                                @else
                                                    <th class="text-end">{{ __('account.operator_package_services.col_items') }}</th>
                                                @endif
                                                <th class="text-end text-nowrap">{{ __('account.operator_package_services.col_actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($packages as $package)
                                                @if ($serviceOfferFilter !== null)
                                                    @foreach ($package->items as $item)
                                                        <tr>
                                                            <td class="fw-medium">{{ $package->displayLabel() }}</td>
                                                            <td>
                                                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                                    {{ __('account.operator_packages.status.' . $package->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $item->day_number ?? '—' }}</td>
                                                            <td>{{ number_format((int) $item->quantity) }}</td>
                                                            <td>{{ __('account.operator_packages.inclusion_mode.' . ($item->inclusion_mode ?? 'included')) }}</td>
                                                            <td class="text-end text-nowrap">
                                                                <a href="{{ route('account.operator-packages.edit', $package) }}" class="btn btn-sm btn-outline-primary">
                                                                    {{ __('account.operator_package_services.edit_package') }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="fw-medium">{{ $package->displayLabel() }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                                {{ __('account.operator_packages.status.' . $package->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">{{ number_format((int) $package->items_count) }}</td>
                                                        <td class="text-end text-nowrap">
                                                            <a href="{{ route('account.operator-packages.edit', $package) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.operator_package_services.edit_package') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
