@extends('layouts.base', ['title' => 'Restablecer contraseña'])

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

                                <h6 class="h5 mb-0 mt-3">Nueva contraseña</h6>
                                <p class="text-muted mt-1 mb-4">Ingresá tu email y la nueva contraseña.</p>

                                <form method="POST" action="{{ route('password.update') }}" class="authentication-form">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <x-form-validation-summary />

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <small>*</small></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                               name="email" value="{{ old('email', $request->email) }}"
                                               required autofocus/>
                                        <x-form-field-error name="email" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nueva contraseña <small>*</small></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                                   name="password" placeholder="Mínimo 8 caracteres" required minlength="8"
                                                   autocomplete="new-password"/>
                                            <button type="button" class="btn btn-outline-secondary px-2" id="btn-toggle-reset-password"
                                                    aria-pressed="false"
                                                    aria-label="{{ __('auth.register.password_show') }}"
                                                    title="{{ __('auth.register.password_show') }}"
                                                    data-label-show="{{ __('auth.register.password_show') }}"
                                                    data-label-hide="{{ __('auth.register.password_hide') }}">
                                                <span id="reset-password-toggle-icon-host"><i data-feather="eye" class="icon icon-xs"></i></span>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary px-2" id="btn-generate-reset-password"
                                                    aria-label="{{ __('auth.register.password_generate') }}"
                                                    title="{{ __('auth.register.password_generate') }}">
                                                <i data-feather="key" class="icon icon-xs"></i>
                                            </button>
                                        </div>
                                        <x-form-field-error name="password" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar contraseña <small>*</small></label>
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation"
                                               name="password_confirmation" placeholder="Repetí la contraseña" required minlength="8"
                                               autocomplete="new-password"/>
                                        <x-form-field-error name="password_confirmation" />
                                    </div>

                                    <div class="mb-0 text-center pt-3 d-grid">
                                        <button class="btn btn-primary" type="submit">Restablecer contraseña</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Volver a <a href="{{ route('login') }}"
                                                             class="text-primary fw-semibold ms-1">Ingresar</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

            var newPassword = document.getElementById('password');
            var confirmPassword = document.getElementById('password_confirmation');
            var btnToggleNew = document.getElementById('btn-toggle-reset-password');
            var hostNew = document.getElementById('reset-password-toggle-icon-host');
            var btnGenerate = document.getElementById('btn-generate-reset-password');
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
