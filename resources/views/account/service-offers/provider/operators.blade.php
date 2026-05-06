@extends('layouts.base', ['title' => __('account.service_offers.provider_operators_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.service_offers.provider_operators_heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">{{ __('account.service_offers.provider_operators_intro') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($relationships->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.service_offers.provider_operators_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.service_offers.provider_operators_col_operator') }}</th>
                                                <th class="text-end">{{ __('account.service_offers.provider_operators_col_action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($relationships as $relationship)
                                                @php
                                                    $operator = $relationship->operatorAccount;
                                                @endphp
                                                @if (! $operator)
                                                    @continue
                                                @endif
                                                @php
                                                    $label = $operator->commercial_name ?? $operator->name ?? ('#' . $operator->id);
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $label }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.service-offers.operators.edit', $operator) }}" class="btn btn-sm btn-primary">
                                                            {{ __('account.relationships.actions.manage_offers') }}
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
