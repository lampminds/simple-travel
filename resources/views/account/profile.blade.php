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
                                            <label for="name" class="form-label">{{ __('profile.name') }}</label>
                                            <input type="text" class="form-control @error('name', 'profile') is-invalid @enderror" id="name"
                                                   name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"/>
                                            <x-form-field-error name="name" bag="profile" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('profile.email') }}</label>
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
                                    <label for="current_password" class="form-label">{{ __('profile.current_password') }}</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('current_password', 'password') is-invalid @enderror" id="current_password"
                                               name="current_password" autocomplete="current-password"/>
                                        <button type="button" class="btn btn-outline-secondary px-2" id="btn-toggle-profile-current-password"
                                                aria-pressed="false"
                                                aria-label="{{ __('auth.register.password_show') }}"
                                                title="{{ __('auth.register.password_show') }}"
                                                data-label-show="{{ __('auth.register.password_show') }}"
                                                data-label-hide="{{ __('auth.register.password_hide') }}">
                                            <span id="profile-current-password-toggle-icon-host"><i data-feather="eye" class="icon icon-xs"></i></span>
                                        </button>
                                    </div>
                                    <x-form-field-error name="current_password" bag="password" />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{ __('profile.new_password') }}</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password', 'password') is-invalid @enderror" id="password"
                                                       name="password" autocomplete="new-password" minlength="8"/>
                                                <button type="button" class="btn btn-outline-secondary px-2" id="btn-toggle-profile-new-password"
                                                        aria-pressed="false"
                                                        aria-label="{{ __('auth.register.password_show') }}"
                                                        title="{{ __('auth.register.password_show') }}"
                                                        data-label-show="{{ __('auth.register.password_show') }}"
                                                        data-label-hide="{{ __('auth.register.password_hide') }}">
                                                    <span id="profile-new-password-toggle-icon-host"><i data-feather="eye" class="icon icon-xs"></i></span>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary px-2" id="btn-generate-profile-password"
                                                        aria-label="{{ __('auth.register.password_generate') }}"
                                                        title="{{ __('auth.register.password_generate') }}">
                                                    <i data-feather="key" class="icon icon-xs"></i>
                                                </button>
                                            </div>
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

@section('script-bottom')
    @include('partials.password-toggle-feather')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function generateSecurePassword(length) {
                var lower = 'abcdefghijklmnopqrstuvwxyz';
                var upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var digits = '0123456789';
                var symbols = '!@#$%&*-_=+';
                var all = lower + upper + digits + symbols;
                function rand(max) {
                    var a = new Uint32Array(1);
                    crypto.getRandomValues(a);
                    return a[0] % max;
                }
                var chars = [
                    lower[rand(lower.length)],
                    upper[rand(upper.length)],
                    digits[rand(digits.length)],
                    symbols[rand(symbols.length)],
                ];
                for (var i = chars.length; i < length; i++) {
                    chars.push(all[rand(all.length)]);
                }
                for (var j = chars.length - 1; j > 0; j--) {
                    var k = rand(j + 1);
                    var t = chars[j];
                    chars[j] = chars[k];
                    chars[k] = t;
                }
                return chars.join('');
            }

            function bindToggle(btn, host, input, getVisible, setVisible) {
                if (!btn || !host || !input) {
                    return;
                }
                btn.addEventListener('click', function () {
                    var next = !getVisible();
                    setVisible(next);
                    input.type = next ? 'text' : 'password';
                    btn.setAttribute('aria-pressed', next ? 'true' : 'false');
                    var showLbl = btn.dataset.labelShow;
                    var hideLbl = btn.dataset.labelHide;
                    var lbl = next ? hideLbl : showLbl;
                    btn.title = lbl;
                    btn.setAttribute('aria-label', lbl);
                    if (typeof window.renderPasswordToggleFeatherIcon === 'function') {
                        window.renderPasswordToggleFeatherIcon(host, next);
                    }
                });
            }

            var currentPassword = document.getElementById('current_password');
            var btnToggleCurrent = document.getElementById('btn-toggle-profile-current-password');
            var hostCurrent = document.getElementById('profile-current-password-toggle-icon-host');
            var currentVisible = false;
            bindToggle(btnToggleCurrent, hostCurrent, currentPassword, function () {
                return currentVisible;
            }, function (v) {
                currentVisible = v;
            });

            var newPassword = document.getElementById('password');
            var confirmPassword = document.getElementById('password_confirmation');
            var btnToggleNew = document.getElementById('btn-toggle-profile-new-password');
            var hostNew = document.getElementById('profile-new-password-toggle-icon-host');
            var btnGenerate = document.getElementById('btn-generate-profile-password');
            var newPairVisible = false;

            if (btnToggleNew && hostNew && newPassword && confirmPassword) {
                btnToggleNew.addEventListener('click', function () {
                    newPairVisible = !newPairVisible;
                    var type = newPairVisible ? 'text' : 'password';
                    newPassword.type = type;
                    confirmPassword.type = type;
                    btnToggleNew.setAttribute('aria-pressed', newPairVisible ? 'true' : 'false');
                    var showLbl = btnToggleNew.dataset.labelShow;
                    var hideLbl = btnToggleNew.dataset.labelHide;
                    var lbl = newPairVisible ? hideLbl : showLbl;
                    btnToggleNew.title = lbl;
                    btnToggleNew.setAttribute('aria-label', lbl);
                    if (typeof window.renderPasswordToggleFeatherIcon === 'function') {
                        window.renderPasswordToggleFeatherIcon(hostNew, newPairVisible);
                    }
                });
            }

            if (btnGenerate && newPassword && confirmPassword) {
                btnGenerate.addEventListener('click', function () {
                    var pwd = generateSecurePassword(16);
                    newPassword.value = pwd;
                    confirmPassword.value = pwd;
                    newPairVisible = true;
                    newPassword.type = 'text';
                    confirmPassword.type = 'text';
                    if (btnToggleNew && hostNew) {
                        btnToggleNew.setAttribute('aria-pressed', 'true');
                        var hideLbl = btnToggleNew.dataset.labelHide;
                        btnToggleNew.title = hideLbl;
                        btnToggleNew.setAttribute('aria-label', hideLbl);
                        if (typeof window.renderPasswordToggleFeatherIcon === 'function') {
                            window.renderPasswordToggleFeatherIcon(hostNew, true);
                        }
                    }
                    newPassword.dispatchEvent(new Event('input', { bubbles: true }));
                    confirmPassword.dispatchEvent(new Event('input', { bubbles: true }));
                });
            }
        });
    </script>
@endsection
