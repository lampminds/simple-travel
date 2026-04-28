@extends('layouts.base', ['title' => __('auth.login.title')])

@section('content')
    <div class="bg-gradient2 min-vh-100 align-items-center d-flex justify-content-center pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-md-5 shadow">
                                    <div class="p-xl-5 p-3">
                                        <div class="mx-auto mb-5">
                                            <x-site-logo class="d-flex align-self-center" />
                                        </div>

                                        <h6 class="h5 mb-0 mt-3">{{ __('auth.login.heading') }}</h6>
                                        <p class="text-muted mt-1 mb-4">
                                            {{ __('auth.login.intro') }}
                                        </p>
                                        @if (session('status'))
                                            <div class="alert alert-warning mb-3" role="alert">{{ session('status') }}</div>
                                        @endif
                                        @if (session('error'))
                                            <div class="alert alert-danger mb-3" role="alert">{{ session('error') }}</div>
                                        @endif
                                        <!--form start-->
                                        <form method="POST" action="{{ route('login') }}" class="authentication-form">

                                            @csrf
                                            <x-form-validation-summary />

                                            <div class="mb-3">
                                                <label for="email" class="form-label required-label">{{ __('auth.login.email') }}</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                                       placeholder="{{ __('auth.login.placeholder_email') }}" name="email" value="{{ old('email') }}"/>
                                                <x-form-field-error name="email" />
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label required-label" for="password">{{ __('auth.login.password') }}</label>
                                                <a href="{{ route('password.request') }}"
                                                   class="float-end text-muted text-unline-dashed ms-1 fs-13">{{ __('auth.login.forgot_password') }}</a>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                                       name="password" placeholder="{{ __('auth.login.placeholder_password') }}" required/>
                                                <x-form-field-error name="password" />
                                            </div>

                                            <div class="mb-3">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember"/>
                                                <label class="form-check-label text-muted" for="checkbox-signin">{{ __('auth.login.remember_me') }}</label>
                                            </div>

                                            <div class="mb-0 text-center d-grid">
                                                <button class="btn btn-primary" type="submit">{{ __('auth.login.submit') }}</button>
                                            </div>
                                        </form>
                                        <!--/.form end-->

                                        <div class="py-3 text-center"><span
                                                class="fs-13 fw-bold">{{ __('auth.login.or') }}</span>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6 text-center">
                                                <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-white w-100">
                                                    <i data-feather="chrome" class='icon icon-xs me-2'></i>{{ __('auth.login.google') }}
                                                </a>
                                            </div>
                                            <div class="col-12 col-md-6 text-center">
                                                <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="btn btn-white w-100">
                                                    <i data-feather="facebook" class='icon icon-xs me-2'></i>{{ __('auth.login.facebook') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 offset-md-1 d-none d-md-flex align-items-center justify-content-center">
                                    <div class="py-2 py-xl-3 text-center w-100">
                                        <img
                                            src="{{ asset('images/robots/login-sm.png') }}"
                                            alt=""
                                            class="img-fluid"
                                            style="max-height: 520px; width: auto;"
                                            loading="lazy"
                                        />
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">{{ __('auth.login.no_account') }} <a href="{{ route('register') }}"
                                    class="text-primary fw-semibold ms-1">{{ __('auth.login.create_account') }}</a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection
