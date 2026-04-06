@extends('layouts.base', ['title' => __('auth.verification.title')])

@section('content')
    <div class="bg-gradient2 min-vh-100 align-items-center d-flex justify-content-center pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="p-xl-5 p-3">
                                <div class="mx-auto mb-5">
                                    <x-site-logo class="d-flex align-self-center" />
                                </div>

                                <h6 class="h5 mb-0 mt-3">{{ __('auth.verification.heading') }}</h6>
                                <p class="text-muted mt-1 mb-4">
                                    {{ __('auth.verification.intro') }}
                                </p>

                                @if (session('status'))
                                    <div class="alert alert-success">
                                        @if (session('status') === 'verification-link-sent')
                                            {{ __('auth.verification.link_sent') }}
                                        @else
                                            {{ session('status') }}
                                        @endif
                                    </div>
                                @endif

                                <p class="text-muted mb-4">
                                    {{ __('auth.verification.no_email') }}
                                </p>

                                <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('auth.verification.resend_button') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <a href="{{ route('account.dashboard') }}" class="btn btn-primary">
                                {{ __('auth.verification.continue') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
