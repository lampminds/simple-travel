@extends('layouts.base', ['title' => __('account.package_availability.index_page_title')])

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
                            :title="__('account.package_availability.index_title')"
                            :instructions="__('account.package_availability.index_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.package-offers.index', ['as' => 'operator']) }}" class="btn btn-outline-primary">
                                {{ __('account.package_availability.manage_offers_link') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($catalogs->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.package_availability.index_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.package_availability.columns.package') }}</th>
                                                <th>{{ __('account.package_availability.columns.inventory') }}</th>
                                                <th>{{ __('account.package_availability.columns.rules_count') }}</th>
                                                <th>{{ __('account.package_availability.columns.overrides_count') }}</th>
                                                <th class="text-end">{{ __('account.package_availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($catalogs as $catalog)
                                                @php
                                                    $inventoryLabel = __('wizard.variant_inventory.' . ($catalog->inventory_type ?? 'unlimited'));
                                                    if (($catalog->inventory_type ?? 'unlimited') !== 'unlimited' && $catalog->inventory_total !== null) {
                                                        $inventoryLabel .= ' (' . number_format((int) $catalog->inventory_total) . ')';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $catalog->displayLabel() }}</td>
                                                    <td>{{ $inventoryLabel }}</td>
                                                    <td>{{ number_format((int) $catalog->availability_rules_count) }}</td>
                                                    <td>{{ number_format((int) $catalog->availability_overrides_count) }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.package-availability.catalogs.show', $catalog) }}" class="btn btn-sm btn-primary">
                                                            {{ __('account.package_availability.manage_button') }}
                                                        </a>
                                                    </td>
                                                </tr>
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
