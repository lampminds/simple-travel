@extends('layouts.base', ['title' => __('account.package_offers.operator_agencies_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.package_offers.operator_agencies_heading')"
                        :instructions="__('account.package_offers.operator_agencies_intro')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($rows->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.package_offers.operator_agencies_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.package_offers.operator_agencies_col_agency') }}</th>
                                                <th class="text-center">{{ __('account.package_offers.operator_agencies_col_offered') }}</th>
                                                <th class="text-center">{{ __('account.package_offers.operator_agencies_col_accepted') }}</th>
                                                <th class="text-end">{{ __('account.package_offers.operator_agencies_col_action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $row)
                                                @php
                                                    $agency = $row['counterpart'];
                                                    $relationship = $row['relationship'];
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $row['counterpart_label'] }}</td>
                                                    <td class="text-center text-muted">{{ (int) ($relationship->offered_package_count ?? 0) }}</td>
                                                    <td class="text-center text-muted">{{ (int) ($relationship->accepted_package_count ?? 0) }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.package-offers.agencies.edit', $agency) }}" class="btn btn-sm btn-primary">
                                                            {{ __('account.relationships.actions.manage_package_offers') }}
                                                        </a>
                                                        @if ((int) ($relationship->accepted_package_count ?? 0) > 0)
                                                            <a href="{{ route('account.package-allocations.agencies.index', $agency) }}" class="btn btn-sm btn-outline-primary ms-1">
                                                                {{ __('account.package_offers.operator_agencies_allocations_button') }}
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
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
