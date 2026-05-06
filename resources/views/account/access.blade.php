@extends('layouts.base', ['title' => __('profile.access_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('profile.access_heading') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('profile.access_subtitle') }}</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row mt-2">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('profile.access_heading') }}</h5>
                            <form method="post" action="{{ route('account.access.update') }}">
                                @csrf
                                @method('PUT')
                                <x-form-validation-summary bag="access" />

                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('profile.email') }}</label>
                                    <input type="email" class="form-control @error('email', 'access') is-invalid @enderror" id="email"
                                           name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"/>
                                    <x-form-field-error name="email" bag="access" />
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('profile.save') }}</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('profile.password_heading') }}</h5>
                            <form method="post" action="{{ route('account.access.password') }}">
                                @csrf
                                @method('PUT')
                                <x-form-validation-summary bag="password" />

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">{{ __('profile.current_password') }}</label>
                                    <input type="password" class="form-control @error('current_password', 'password') is-invalid @enderror" id="current_password"
                                           name="current_password" autocomplete="current-password"/>
                                    <x-form-field-error name="current_password" bag="password" />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{ __('profile.new_password') }}</label>
                                            <input type="password" class="form-control @error('password', 'password') is-invalid @enderror" id="password"
                                                   name="password" autocomplete="new-password" minlength="8"/>
                                            <x-form-field-error name="password" bag="password" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">{{ __('profile.confirm_password') }}</label>
                                            <input type="password" class="form-control @error('password_confirmation', 'password') is-invalid @enderror" id="password_confirmation"
                                                   name="password_confirmation" autocomplete="new-password" minlength="8"/>
                                            <x-form-field-error name="password_confirmation" bag="password" />
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('profile.update_password') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
