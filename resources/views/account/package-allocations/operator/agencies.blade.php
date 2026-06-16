@extends('layouts.base', ['title' => __('account.package_allocations.agencies_page_title')])

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
                            :title="__('account.package_allocations.agencies_heading')"
                            :instructions="__('account.package_allocations.agencies_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.package-offers.index', ['as' => 'operator']) }}" class="btn btn-outline-primary">
                                {{ __('account.package_allocations.manage_offers_link') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($relationships->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.package_allocations.agencies_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.package_allocations.agencies_col_agency') }}</th>
                                                <th>{{ __('account.package_allocations.agencies_col_count') }}</th>
                                                <th class="text-end">{{ __('account.package_allocations.agencies_col_action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($relationships as $relationship)
                                                @php
                                                    $agency = $relationship->providerAccount;
                                                @endphp
                                                @if (! $agency)
                                                    @continue
                                                @endif
                                                @php
                                                    $label = $agency->commercial_name ?? $agency->name ?? ('#' . $agency->id);
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $label }}</td>
                                                    <td>{{ trans_choice('account.package_allocations.agencies_allocations_count', (int) $relationship->allocations_count, ['count' => (int) $relationship->allocations_count]) }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.package-allocations.agencies.index', $agency) }}" class="btn btn-sm btn-primary">
                                                            {{ __('account.package_allocations.agencies_manage_button') }}
                                                        </a>
                                                        <a href="{{ route('account.package-offers.agencies.edit', $agency) }}" class="btn btn-sm btn-outline-primary ms-1">
                                                            {{ __('account.package_allocations.manage_offers_link') }}
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
