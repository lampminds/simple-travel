@extends('layouts.base', ['title' => __('profile.title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('profile.heading') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('profile.subtitle') }}</p>
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
                            <h5 class="card-title mb-3">{{ __('profile.avatar_heading') }}</h5>
                            <p class="text-muted small mb-3">{{ __('profile.avatar_help') }}</p>

                            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                <img src="{{ $user->avatarDisplayUrl() }}" width="128" height="128"
                                     class="rounded-circle border shadow-sm bg-light" alt="{{ $user->name }}"
                                     style="object-fit: cover;"/>
                                <div class="flex-grow-1">
                                    <form method="post" action="{{ route('account.profile.avatar') }}" enctype="multipart/form-data" class="mb-2">
                                        @csrf
                                        <x-form-validation-summary bag="avatar" />
                                        <label for="avatar" class="form-label">{{ __('profile.avatar_file_label') }}</label>
                                        <input type="file" name="avatar" id="avatar"
                                               class="form-control @error('avatar', 'avatar') is-invalid @enderror"
                                               accept="image/jpeg,image/png,image/gif,image/webp"/>
                                        <x-form-field-error name="avatar" bag="avatar" />
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">{{ __('profile.avatar_upload') }}</button>
                                    </form>
                                    @if ($user->hasUploadedAvatar())
                                        <form method="post" action="{{ route('account.profile.avatar.destroy') }}" class="d-inline"
                                              onsubmit="return confirm(@json(__('profile.avatar_remove_confirm')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('profile.avatar_remove') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('profile.heading') }}</h5>
                            <form method="post" action="{{ route('account.profile.update') }}">
                                @csrf
                                @method('PUT')
                                <x-form-validation-summary bag="profile" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">{{ __('profile.name') }} <small>*</small></label>
                                            <input type="text" class="form-control @error('name', 'profile') is-invalid @enderror" id="name"
                                                   name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"/>
                                            <x-form-field-error name="name" bag="profile" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('profile.email') }} <small>*</small></label>
                                            <input type="email" class="form-control @error('email', 'profile') is-invalid @enderror" id="email"
                                                   name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"/>
                                            <x-form-field-error name="email" bag="profile" />
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('profile.save') }}</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('profile.password_heading') }}</h5>
                            <form method="post" action="{{ route('account.profile.password') }}">
                                @csrf
                                @method('PUT')
                                <x-form-validation-summary bag="password" />

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">{{ __('profile.current_password') }} <small>*</small></label>
                                    <input type="password" class="form-control @error('current_password', 'password') is-invalid @enderror" id="current_password"
                                           name="current_password" autocomplete="current-password"/>
                                    <x-form-field-error name="current_password" bag="password" />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{ __('profile.new_password') }} <small>*</small></label>
                                            <input type="password" class="form-control @error('password', 'password') is-invalid @enderror" id="password"
                                                   name="password" autocomplete="new-password"/>
                                            <x-form-field-error name="password" bag="password" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">{{ __('profile.confirm_password') }} <small>*</small></label>
                                            <input type="password" class="form-control @error('password_confirmation', 'password') is-invalid @enderror" id="password_confirmation"
                                                   name="password_confirmation" autocomplete="new-password"/>
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
