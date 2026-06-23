@extends('layouts.base', ['title' => __('pricing.page_title')])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true, 'fixedWidth' => true, 'sticky' => true, 'topbarColor' => 'navbar-light', 'classList' => 'ms-auto', 'ctaButtonClass' => 'btn-outline-secondary btn-sm'])

    <section class="hero-4 pb-5 pt-7 py-sm-7 bg-gradient2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="hero-title">{{ __('pricing.hero_title') }}</h1>
                    <p class="fs-17 text-muted mb-4">{{ __('pricing.hero_lead') }}</p>

                    <div class="row g-3 justify-content-center pricing-hero-promos">
                        <div class="col-md-6 col-lg-5">
                            <div class="pricing-hero-promo pricing-hero-promo-trial h-100 text-start">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="pricing-hero-promo-icon bg-soft-primary text-primary flex-shrink-0">
                                        <i data-feather="gift" class="icon icon-sm"></i>
                                    </span>
                                    <div>
                                        <span class="badge rounded-pill badge-soft-primary px-2 py-1 mb-2">{{ __('pricing.hero_promo_trial_badge') }}</span>
                                        <div class="fw-bold fs-18 text-dark lh-sm mb-1">{{ __('pricing.hero_promo_trial_title') }}</div>
                                        <p class="text-muted small mb-0">{{ __('pricing.hero_promo_trial_text') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-5">
                            <div class="pricing-hero-promo pricing-hero-promo-setup h-100 text-start">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="pricing-hero-promo-icon bg-soft-success text-success flex-shrink-0">
                                        <i data-feather="check-circle" class="icon icon-sm"></i>
                                    </span>
                                    <div>
                                        <span class="badge rounded-pill badge-soft-success px-2 py-1 mb-2">{{ __('pricing.hero_promo_setup_badge') }}</span>
                                        <div class="fw-bold fs-18 text-dark lh-sm mb-1">{{ __('pricing.hero_promo_setup_title') }}</div>
                                        <p class="text-muted small mb-0">{{ __('pricing.hero_promo_setup_text') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .pricing-hero-promo {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1rem;
            padding: 1.125rem 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .pricing-hero-promo-trial {
            border-top: 3px solid var(--bs-primary);
        }

        .pricing-hero-promo-setup {
            border-top: 3px solid var(--bs-success);
        }

        .pricing-hero-promo-icon {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (min-width: 768px) {
            .pricing-hero-promos {
                margin-top: 0.25rem;
            }
        }
    </style>

  @php
      $defaultAccountType = collect($pricingConfig['accountTypes'])->firstWhere('code', $pricingConfig['defaultAccountTypeCode'])
          ?? ($pricingConfig['accountTypes'][0] ?? null);
      $defaultCurrency = collect($pricingConfig['currencies'])->firstWhere('id', $pricingConfig['defaultCurrencyId'])
          ?? ($pricingConfig['currencies'][0] ?? null);
  @endphp

  <div data-pricing-page class="pricing-page pb-lg-0 pb-5">
    <style>
        .pricing-mobile-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        @media (min-width: 992px) {
            .pricing-estimate-panel.sticky-top {
                max-height: calc(100vh - 11rem);
            }

            .pricing-estimate-lines {
                max-height: min(18rem, calc(100vh - 22rem));
            }
        }
    </style>
    <section class="section pt-0 pb-3 position-relative">
        <div class="container">
            <div class="card border shadow-sm sticky-top z-3 pricing-config-bar" style="top: 5.5rem;">
                <div class="card-body py-3 px-3 px-lg-4">
                    <button
                        type="button"
                        class="btn btn-link w-100 d-lg-none text-decoration-none text-body p-0 mb-0"
                        data-bs-toggle="collapse"
                        data-bs-target="#pricing-config-collapse"
                        aria-expanded="false"
                        aria-controls="pricing-config-collapse"
                    >
                        <span class="d-flex align-items-center justify-content-between gap-2">
                            <span class="d-flex flex-wrap align-items-center gap-2">
                                <span class="small fw-semibold text-muted">{{ __('pricing.config_bar_label') }}</span>
                                <span class="badge bg-soft-primary text-primary" data-config-summary-role>{{ $defaultAccountType['name'] ?? '' }}</span>
                                <span class="badge bg-light text-muted border" data-config-summary-users>{{ __('pricing.users_summary', ['count' => $pricingConfig['defaultUserCount']]) }}</span>
                                <span class="badge bg-light text-muted border" data-config-summary-currency>{{ $defaultCurrency['code'] ?? 'USD' }}</span>
                            </span>
                            <i data-feather="chevron-down" class="icon icon-xs text-muted flex-shrink-0"></i>
                        </span>
                    </button>

                    <div class="collapse d-lg-block" id="pricing-config-collapse">
                        <div class="d-lg-flex align-items-start align-items-lg-center gap-3 gap-xl-4 flex-wrap pt-3 pt-lg-0">
                            <div class="pricing-config-group flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="small fw-semibold mb-0">{{ __('pricing.step_account_type') }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm p-0 text-muted lh-1"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ __('pricing.step_account_type_help') }}"
                                        aria-label="{{ __('pricing.step_account_type_help') }}"
                                    >?</button>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($pricingConfig['accountTypes'] as $accountType)
                                        <button
                                            type="button"
                                            class="btn btn-sm {{ $accountType['code'] === $pricingConfig['defaultAccountTypeCode'] ? 'btn-primary' : 'btn-outline-primary' }} px-3 py-1 rounded-pill"
                                            data-account-type="{{ $accountType['code'] }}"
                                            data-account-type-name="{{ $accountType['name'] }}"
                                            aria-pressed="{{ $accountType['code'] === $pricingConfig['defaultAccountTypeCode'] ? 'true' : 'false' }}"
                                        >
                                            {{ $accountType['name'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="vr d-none d-xl-block align-self-stretch opacity-25"></div>

                            <div class="pricing-config-group">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="small fw-semibold mb-0">{{ __('pricing.step_users') }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm p-0 text-muted lh-1"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ __('pricing.step_users_help') }}"
                                        aria-label="{{ __('pricing.step_users_help') }}"
                                    >?</button>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <label class="visually-hidden" for="pricing-user-count">{{ __('pricing.users_label') }}</label>
                                    <input
                                        type="number"
                                        id="pricing-user-count"
                                        class="form-control form-control-sm"
                                        style="max-width: 4.5rem;"
                                        min="1"
                                        step="1"
                                        value="{{ $pricingConfig['defaultUserCount'] }}"
                                        data-user-count-input
                                    >
                                    @foreach($pricingConfig['userPresets'] as $preset)
                                        <button
                                            type="button"
                                            class="btn {{ $preset === $pricingConfig['defaultUserCount'] ? 'btn-primary' : 'btn-outline-primary' }} btn-sm rounded-pill px-2 py-0"
                                            style="min-width: 2.25rem;"
                                            data-user-preset="{{ $preset }}"
                                            aria-pressed="{{ $preset === $pricingConfig['defaultUserCount'] ? 'true' : 'false' }}"
                                        >
                                            {{ $preset }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="vr d-none d-xl-block align-self-stretch opacity-25"></div>

                            <div class="pricing-config-group flex-grow-1 flex-xl-grow-0" style="min-width: 12rem;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="small fw-semibold mb-0">{{ __('pricing.step_currency') }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm p-0 text-muted lh-1"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ __('pricing.step_currency_help') }}"
                                        aria-label="{{ __('pricing.step_currency_help') }}"
                                    >?</button>
                                </div>
                                @if(!empty($pricingConfig['currencies']))
                                    <label class="visually-hidden" for="pricing-currency">{{ __('pricing.step_currency') }}</label>
                                    <select id="pricing-currency" class="form-select form-select-sm" data-currency-select>
                                        @foreach($pricingConfig['currencies'] as $currency)
                                            <option
                                                value="{{ $currency['id'] }}"
                                                {{ (int) $currency['id'] === (int) $pricingConfig['defaultCurrencyId'] ? 'selected' : '' }}
                                            >
                                                {{ $currency['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="small text-muted mb-0 mt-1" data-exchange-rate-note></p>
                                @else
                                    <p class="text-muted small mb-0">{{ __('pricing.prices_usd_note') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-2 pb-5 position-relative">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8 col-xl-9">
                    <div data-core-section class="mb-5">
                        <div class="mb-3">
                            <h2 class="display-6 fw-semibold mb-2">{{ __('pricing.core_heading') }}</h2>
                            <p class="text-muted mb-2">{{ __('pricing.core_intro') }}</p>
                            <p class="mb-0">
                                <span class="fw-semibold text-primary py-2 px-3 rounded bg-soft-primary d-inline-block">
                                    {{ __('pricing.block1_highlight') }}
                                </span>
                            </p>
                        </div>
                        <div data-core-card></div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <h2 class="display-6 fw-semibold mb-2">{{ __('pricing.addons_heading') }}</h2>
                            <p class="text-muted mb-0">{{ __('pricing.addons_intro') }}</p>
                        </div>
                        <div class="alert alert-light border d-none" data-addons-empty>
                            {{ __('pricing.no_modules_for_type') }}
                        </div>
                        <div class="row align-items-start" data-addons-grid></div>
                    </div>
                </div>

                <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                    @include('pages.partials.pricing-estimate-panel', ['panelContext' => 'desktop'])
                </div>
            </div>
        </div>
    </section>

    <div class="pricing-mobile-bar d-lg-none border-top bg-white shadow-sm">
        <div class="container py-2">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="min-w-0">
                    <div class="small text-muted">{{ __('pricing.estimate_total') }}</div>
                    <div class="fs-5 fw-bold text-primary text-truncate" data-mobile-breakdown-total></div>
                    <div class="small text-muted text-truncate" data-mobile-breakdown-context></div>
                </div>
                <button
                    type="button"
                    class="btn btn-outline-primary btn-sm flex-shrink-0"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#pricing-estimate-offcanvas"
                    aria-controls="pricing-estimate-offcanvas"
                >
                    {{ __('pricing.mobile_view_detail') }}
                </button>
            </div>
        </div>
    </div>

    <div
        class="offcanvas offcanvas-bottom d-lg-none"
        tabindex="-1"
        id="pricing-estimate-offcanvas"
        aria-labelledby="pricing-estimate-offcanvas-label"
        style="max-height: 85vh;"
    >
        <div class="offcanvas-header border-bottom py-3">
            <h5 class="offcanvas-title fw-semibold" id="pricing-estimate-offcanvas-label">{{ __('pricing.estimate_heading') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('pricing.mobile_close_detail') }}"></button>
        </div>
        <div class="offcanvas-body pt-3">
            @include('pages.partials.pricing-estimate-panel', ['panelContext' => 'mobile'])
        </div>
    </div>
  </div>

    <!-- benefits start -->
    <section class="pt-5 pb-7 career-service position-relative bg-light">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('pricing.benefits_badge') }}</span>
                    <h1 class="display-5 fw-semibold">{{ __('pricing.benefits_heading') }}</h1>
                    <p class="text-muted mx-auto">{{ __('pricing.benefits_subtitle') }}</p>
                </div>
            </div>
            <div class="row" data-aos="fade-up" data-aos-duration="500">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center pe-sm-5 pe-3 mt-lg-5 mt-4">
                        <span class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-sm text-primary me-4 flex-shrink-0">
                            @svg('/duotone-icons/communication/Active-call')
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="mt-0">{{ __('pricing.benefit1_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('pricing.benefit1_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex align-items-center pe-sm-5 mt-lg-5 mt-4">
                        <span class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-md text-primary me-4 flex-shrink-0">
                            @svg('/duotone-icons/map/Compass')
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="mt-0">{{ __('pricing.benefit2_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('pricing.benefit2_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center pe-sm-5 mt-lg-5 mt-4">
                        <span class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-sm text-primary me-4 flex-shrink-0">
                            @svg('/duotone-icons/media/Equalizer')
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="mt-0">{{ __('pricing.benefit3_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('pricing.benefit3_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex align-items-center pe-sm-5 mt-lg-5 mt-4">
                        <span class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-sm text-primary me-4 flex-shrink-0">
                            @svg('/duotone-icons/food/Beer')
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="mt-0">{{ __('pricing.benefit4_title') }}</h5>
                            <p class="text-muted mb-0">{{ __('pricing.benefit4_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- benefits end -->

    <!-- cta starts -->
    <section class="section py-6 position-relative">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col text-center">
                    <h1 class="display-5 fw-semibold">{{ __('pricing.cta_heading') }}</h1>
                    <p class="text-muted mx-auto">{{ __('pricing.cta_subtitle') }}</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-6">
                    <div class="card shadow-none border mb-lg-0 rounded-sm" data-aos="fade-up" data-aos-duration="500">
                        <div class="card-body">
                            <h3 class="mt-0 fw-semibold">{{ __('pricing.cta_contact_title') }}</h3>
                            <p>{{ __('pricing.cta_contact_desc') }}</p>
                            <a href="{{ route('second', ['pages', 'contact']) }}"
                               class="btn btn-outline-primary mt-4">{{ __('pricing.cta_contact_button') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-none border mb-0 rounded-sm" data-aos="fade-up" data-aos-duration="1000">
                        <div class="card-body">
                            <h3 class="mt-0 fw-semibold">{{ __('pricing.cta_kb_title') }}</h3>
                            <p>{{ __('pricing.cta_kb_desc') }}</p>
                            <a href="{{ route('pages.help-center') }}"
                               class="btn btn-outline-primary mt-4">{{ __('pricing.cta_kb_button') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- cta end -->

    <x-site-footer class="mt-lg-5" />
@endsection

@section('script')
    <script type="application/json" id="pricing-config">@json($pricingConfig)</script>
@endsection
