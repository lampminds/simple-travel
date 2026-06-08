@extends('layouts.base', ['title' => __('account.allocations.operators_page_title')])

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
                        :title="__('account.allocations.operators_heading')"
                        :instructions="__('account.allocations.operators_intro_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($relationships->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.allocations.operators_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.allocations.operators_col_operator') }}</th>
                                                <th>{{ __('account.allocations.operators_col_count') }}</th>
                                                <th class="text-end">{{ __('account.allocations.operators_col_action') }}</th>
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
                                                    <td>{{ trans_choice('account.allocations.operators_allocations_count', (int) $relationship->allocations_count, ['count' => (int) $relationship->allocations_count]) }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.allocations.operators.index', $operator) }}" class="btn btn-sm btn-primary">
                                                            {{ __('account.allocations.operators_manage_button') }}
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
