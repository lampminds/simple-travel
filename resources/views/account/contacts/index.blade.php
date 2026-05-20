@extends('layouts.base', ['title' => __('account.contacts.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.contacts.heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">{{ __('account.contacts.intro') }}</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mt-3 mb-0" role="alert">{{ session('status') }}</div>
            @endif

            <div class="mt-4">
                @if ($groups->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-muted">{{ __('account.contacts.empty') }}</div>
                    </div>
                @else
                    <div class="vstack gap-4">
                        @foreach ($groups as $group)
                            @php
                                $sourceAccount = $group['account'];
                                $companyName = $sourceAccount->commercial_name ?? $sourceAccount->name ?? $sourceAccount->nick;
                            @endphp
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h5 class="mb-0 fw-semibold">{{ $companyName }}</h5>
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($group['contacts'] as $entry)
                                        @php
                                            $accountPerson = $entry['account_person'];
                                            $person = $entry['person'];
                                        @endphp
                                        <li class="list-group-item py-3">
                                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-2">
                                                <div class="min-w-0">
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <span class="fw-semibold">{{ $person->name }}</span>
                                                        @if ($entry['is_preferred'])
                                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                                                                {{ __('account.contacts.preferred_badge') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($entry['email'])
                                                        <div class="text-muted small mt-1">
                                                            <a href="mailto:{{ e($entry['email']) }}" class="text-muted text-decoration-none">{{ $entry['email'] }}</a>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="{{ route('account.contacts.show', $accountPerson) }}" class="btn btn-outline-primary btn-sm">
                                                        {{ __('account.contacts.view_details') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
