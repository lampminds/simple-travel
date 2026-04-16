@extends('layouts.base', ['title' => __('account.select_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-2">{{ __('account.select_heading') }}</h3>
                            <p class="text-muted mb-4">{{ __('account.select_intro') }}</p>

                            <div class="d-grid gap-2">
                                @foreach($accounts as $account)
                                    <form method="POST" action="{{ route('account.switch') }}">
                                        @csrf
                                        <input type="hidden" name="account_id" value="{{ $account->id }}">
                                        <input type="hidden" name="redirect_to" value="{{ route('account.dashboard', absolute: false) }}">
                                        <button type="submit" class="btn btn-outline-primary w-100 text-start">
                                            <span class="fw-semibold">{{ $account->commercial_name ?? $account->name ?? $account->nick }}</span>
                                            @if($account->code)
                                                <span class="d-block small text-muted">{{ $account->code }}</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection

