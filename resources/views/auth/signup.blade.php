@extends('layouts.base', ['title' => __('auth.register.title')])

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

                                        @php
                                            $inv = $invitation ?? null;
                                            $mode = $invitationMode ?? null;
                                        @endphp
                                        <h6 class="h5 mb-0 mt-3">{{ __('auth.register.heading') }}</h6>
                                        <p class="text-muted mt-1 mb-4">
                                            @if ($mode === 'internal')
                                                {{ __('auth.register.intro_internal') }}
                                            @elseif ($mode === 'external')
                                                {{ __('auth.register.intro_external') }}
                                            @else
                                                {{ __('auth.register.intro') }}
                                            @endif
                                        </p>
                                        @if ($inv?->invitedBy)
                                            <p class="text-muted mb-4 small">
                                                {{ __('auth.register.invitation_from', [
                                                    'name' => $inv->invitedBy->name,
                                                    'company' => $inv->accountInviting?->commercial_name
                                                        ?? $inv->accountInviting?->name
                                                        ?? $inv->account?->commercial_name
                                                        ?? $inv->account?->name
                                                        ?? '—',
                                                ]) }}
                                            </p>
                                        @endif

                                        <div class="alert alert-warning border-0 mb-4" role="alert">
                                            <small>
                                                <strong>{{ __('auth.register.important') }}</strong>
                                                @if ($mode === 'internal')
                                                    {{ __('auth.register.important_text_internal') }}
                                                @elseif ($mode === 'external')
                                                    {{ __('auth.register.important_text_external') }}
                                                @else
                                                    {{ __('auth.register.important_text') }}
                                                @endif
                                            </small>
                                        </div>

                                        <form method="POST" action="{{ route('register') }}" class="authentication-form">
                                            @csrf
                                            <x-form-validation-summary />
                                            @if ($inv)
                                                <input type="hidden" name="invitation_token" value="{{ $inv->token }}"/>
                                            @endif

                                            @if ($mode === 'internal')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('auth.register.invited_company') }}</label>
                                                    <input type="text" class="form-control" readonly
                                                           value="{{ $inv->account?->commercial_name ?? $inv->account?->name ?? '—' }}"/>
                                                </div>
                                            @endif

                                            @unless ($mode === 'internal')
                                            <div class="mb-3">
                                                <label for="company_name" class="form-label">{{ __('auth.register.company_name') }}</label>
                                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name"
                                                       placeholder="{{ __('auth.register.placeholder_company_name') }}" name="company_name"
                                                       value="{{ old('company_name') }}" required
                                                       @if($mode !== 'internal') autofocus @endif />
                                                <x-form-field-error name="company_name" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="company_type" class="form-label">{{ __('auth.register.company_type') }}</label>
                                                <select class="form-select @error('company_type') is-invalid @enderror" id="company_type" name="company_type" @if($mode !== 'internal') required @endif>
                                                    <option value="">{{ __('auth.register.select_type') }}</option>
                                                    @foreach($companyTypes ?? [] as $id => $data)
                                                        @php
                                                            $name = is_array($data) ? ($data['name'] ?? '') : $data;
                                                            $description = is_array($data) ? ($data['description'] ?? '') : '';
                                                        @endphp
                                                        <option value="{{ $id }}" {{ old('company_type') == $id ? 'selected' : '' }}
                                                                title="{{ e($description) }}"
                                                                data-description="{{ e($description) }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <x-form-field-error name="company_type" />
                                                <div id="company_type_description" class="small text-muted mt-2" style="min-height: 1.5em;" aria-live="polite"></div>
                                            </div>
                                            @endunless

                                            <div class="mb-3">
                                                <label for="name" class="form-label">{{ __('auth.register.your_name') }}</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                                       placeholder="{{ __('auth.register.placeholder_name') }}" name="name"
                                                       value="{{ old('name', $inv?->name) }}" required @if($mode === 'internal') autofocus @endif/>
                                                <x-form-field-error name="name" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label">{{ __('auth.register.email') }}</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                                       placeholder="{{ __('auth.register.placeholder_email') }}" name="email"
                                                       value="{{ old('email', $inv?->email) }}"
                                                       required autocomplete="email"/>
                                                <x-form-field-error name="email" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">{{ __('auth.register.password') }}</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                                           name="password" placeholder="{{ __('auth.register.placeholder_password') }}" required
                                                           autocomplete="new-password" minlength="8"/>
                                                    <button type="button" class="btn btn-outline-secondary px-2" id="btn-toggle-password"
                                                            aria-pressed="false"
                                                            aria-label="{{ __('auth.register.password_show') }}"
                                                            title="{{ __('auth.register.password_show') }}"
                                                            data-label-show="{{ __('auth.register.password_show') }}"
                                                            data-label-hide="{{ __('auth.register.password_hide') }}">
                                                        <span id="password-toggle-icon-host"><i data-feather="eye" class="icon icon-xs"></i></span>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary px-2" id="btn-generate-password"
                                                            aria-label="{{ __('auth.register.password_generate') }}"
                                                            title="{{ __('auth.register.password_generate') }}">
                                                        <i data-feather="key" class="icon icon-xs"></i>
                                                    </button>
                                                </div>
                                                <x-form-field-error name="password" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">{{ __('auth.register.password_confirmation') }}</label>
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation"
                                                       name="password_confirmation" placeholder="{{ __('auth.register.placeholder_password_confirmation') }}" required
                                                       autocomplete="new-password" minlength="8"/>
                                                <x-form-field-error name="password_confirmation" />
                                            </div>

                                            <div class="mb-0 text-center d-grid">
                                                <button class="btn btn-primary" type="submit">{{ __('auth.register.submit') }}</button>
                                            </div>
                                        </form>
                                        <div class="py-3 text-center"><span
                                                class="fs-13 fw-bold">{{ __('auth.register.or') }}</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <a href="" class="btn btn-white w-100">
                                                    <i data-feather="github" class='icon icon-xs me-2'></i>{{ __('auth.register.signup_github') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 offset-md-1 d-none d-md-inline-block">
                                    <div class="position-relative mt-5 pt-5">
                                        <!--swiper start-->
                                        <div class="swiper auth-swiper">
                                            <div class="swiper-wrapper">
                                                <div class="swiper-slide">
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas1-{{ app()->getLocale() }}.png" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">{{ __('auth.register.carousel_slide1_title') }}</h5>
                                                            <p class="text-muted">{{ __('auth.register.carousel_slide1_text') }}</p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- swiper-slide End -->
                                                <div class="swiper-slide">

                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas2.jpg" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">{{ __('auth.register.carousel_slide2_title') }}</h5>
                                                            <p class="text-muted">{{ __('auth.register.carousel_slide2_text') }}</p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- swiper-slide End -->
                                                <div class="swiper-slide">

                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas3.jpg" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">{{ __('auth.register.carousel_slide3_title') }}</h5>
                                                            <p class="text-muted">{{ __('auth.register.carousel_slide3_text') }}</p>
                                                        </div>

                                                    </div>
                                                </div>
                                                <!-- swiper-slide End -->
                                            </div>
                                            <!-- swiper-wrapper End -->
                                            <div class="swiper-pagination"></div>
                                        </div>
                                        <!--swiper end-->
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">{{ __('auth.register.already_have_account') }} <a href="{{ route('login') }}"
                                    class="text-primary fw-semibold ms-1">{{ __('auth.register.login') }}</a></p>
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

@section('script-bottom')
@include('partials.password-toggle-feather')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const btnToggle = document.getElementById('btn-toggle-password');
    const btnGenerate = document.getElementById('btn-generate-password');

    if (password && passwordConfirmation && btnToggle && btnGenerate) {
        let passwordsVisible = false;

        const toggleIconHost = document.getElementById('password-toggle-icon-host');

        function setPasswordVisibility(visible) {
            passwordsVisible = visible;
            const type = visible ? 'text' : 'password';
            password.type = type;
            passwordConfirmation.type = type;
            btnToggle.setAttribute('aria-pressed', visible ? 'true' : 'false');
            const showLbl = btnToggle.dataset.labelShow;
            const hideLbl = btnToggle.dataset.labelHide;
            const nextTitle = visible ? hideLbl : showLbl;
            btnToggle.title = nextTitle;
            btnToggle.setAttribute('aria-label', nextTitle);
            if (typeof window.renderPasswordToggleFeatherIcon === 'function') {
                window.renderPasswordToggleFeatherIcon(toggleIconHost, visible);
            }
        }

        btnToggle.addEventListener('click', function () {
            setPasswordVisibility(!passwordsVisible);
        });

        function generateSecurePassword(length) {
            const lower = 'abcdefghijklmnopqrstuvwxyz';
            const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const digits = '0123456789';
            const symbols = '!@#$%&*-_=+';
            const all = lower + upper + digits + symbols;
            const rand = function (max) {
                const a = new Uint32Array(1);
                crypto.getRandomValues(a);
                return a[0] % max;
            };
            const chars = [
                lower[rand(lower.length)],
                upper[rand(upper.length)],
                digits[rand(digits.length)],
                symbols[rand(symbols.length)],
            ];
            for (let i = chars.length; i < length; i++) {
                chars.push(all[rand(all.length)]);
            }
            for (let i = chars.length - 1; i > 0; i--) {
                const j = rand(i + 1);
                const t = chars[i];
                chars[i] = chars[j];
                chars[j] = t;
            }
            return chars.join('');
        }

        btnGenerate.addEventListener('click', function () {
            const pwd = generateSecurePassword(16);
            password.value = pwd;
            passwordConfirmation.value = pwd;
            setPasswordVisibility(true);
            password.dispatchEvent(new Event('input', { bubbles: true }));
            passwordConfirmation.dispatchEvent(new Event('input', { bubbles: true }));
        });
    }

    @if (($invitationMode ?? null) !== 'internal')
    const select = document.getElementById('company_type');
    const descEl = document.getElementById('company_type_description');
    if (select && descEl) {
        function updateCompanyTypeDescription() {
            const opt = select.options[select.selectedIndex];
            const desc = opt && opt.dataset ? (opt.dataset.description || '') : '';
            descEl.textContent = desc;
        }
        select.addEventListener('change', updateCompanyTypeDescription);
        updateCompanyTypeDescription();
    }
    @endif
});
</script>
@endsection

