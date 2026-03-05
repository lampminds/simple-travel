@extends('layouts.base', ['title' => __('saas.title')])

@section('content')

    <div class="header-2 primary">

        @include('layouts.partials.navbar', ['hideSearch' => true,'fixedWidth' => true, 'sticky' => true,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto','ctaButtonClass' => 'btn-primary btn-sm' ])

    </div>

    <section class="position-relative overflow-hidden hero-13 pt-4 pt-lg-5 pb-6">
        <div class="container">
            <div class="swiper hero-swiper">
                    <div class="swiper-wrapper">
                        {{-- Slide 1 --}}
                        <div class="swiper-slide">
                            <div class="row align-items-center text-center text-sm-start">
                                <div class="col-lg-6">
                                    <div class="mb-lg-0 mb-5">
                                        <h1 class="hero-title">
                                            {!! __('saas.hero_slide1_title') !!}
                                        </h1>
                                        <p class="fs-17 text-muted pt-3">
                                            {!! __('saas.hero_slide1_lead') !!}
                                        </p>
                                        <div class="pt-5">
                                            <div class="row g-2 text-start">
                                                <div class="col-md-4 col-lg-6">
                                                    <label class="visually-hidden" for="email">Email</label>
                                                    <input type="email" class="form-control mb-2 me-sm-2 shadow-sm" name="email"
                                                           id="email" placeholder="{{ __('saas.email_placeholder') }}">
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="submit" class="btn btn-primary mb-2">{{ __('saas.try_it') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mt-1 fs-12">
                                            <div class="me-4">
                                                <i data-feather="check" class="icon icon-dual-success icon-xs me-1"></i>{{ __('saas.demo_free') }}
                                            </div>
                                            <div>
                                                <i data-feather="check" class="icon icon-dual-success icon-xs me-1"></i>{{ __('saas.no_credit_card') }}
                                            </div>
                                        </div>
                                        <div class="pt-5">
                                            <ul class="navbar-nav align-items-lg-center d-flex">
                                                <li class="nav-item ms-2">
                                                    <a class="btn btn-primary btn-sm" href="{{ route('pages.pricing') }}">{{ __('saas.view_plans') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 offset-lg-1 hero-right">
                                    <div class="img-container">
                                        <div class="swiper-slide-content">
                                            <img src="/images/hero/saas1-{{ app()->getLocale() }}.png" alt="" class="img-fluid rounded-lg"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Slide 2 --}}
                        <div class="swiper-slide">
                            <div class="row align-items-center text-center text-sm-start">
                                <div class="col-lg-6">
                                    <div class="mb-lg-0 mb-5">
                                        <h1 class="hero-title">
                                            {!! __('saas.hero_slide2_title') !!}
                                        </h1>
                                        <p class="fs-17 text-muted pt-3">
                                            {!! __('saas.hero_slide2_lead') !!}
                                        </p>
                                        <ul class="list-unstyled pt-3 mb-4">
                                            <li class="d-flex align-items-center py-1">
                                                <i data-feather="check" class="icon icon-dual-success icon-sm me-2 flex-shrink-0"></i>
                                                <span>{{ __('saas.slide2_advantage1') }}</span>
                                            </li>
                                            <li class="d-flex align-items-center py-1">
                                                <i data-feather="check" class="icon icon-dual-success icon-sm me-2 flex-shrink-0"></i>
                                                <span>{{ __('saas.slide2_advantage2') }}</span>
                                            </li>
                                            <li class="d-flex align-items-center py-1">
                                                <i data-feather="check" class="icon icon-dual-success icon-sm me-2 flex-shrink-0"></i>
                                                <span>{{ __('saas.slide2_advantage3') }}</span>
                                            </li>
                                            <li class="d-flex align-items-center py-1">
                                                <i data-feather="check" class="icon icon-dual-success icon-sm me-2 flex-shrink-0"></i>
                                                <span>{{ __('saas.slide2_advantage4') }}</span>
                                            </li>
                                            <li class="d-flex align-items-center py-1">
                                                <i data-feather="check" class="icon icon-dual-success icon-sm me-2 flex-shrink-0"></i>
                                                <span>{{ __('saas.slide2_advantage5') }}</span>
                                            </li>
                                        </ul>
                                        <div class="pt-3">
                                            <ul class="navbar-nav align-items-lg-center d-flex">
                                                <li class="nav-item ms-2">
                                                    <a class="btn btn-primary btn-sm" href="{{ route('pages.pricing') }}">{{ __('saas.view_plans') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 offset-lg-1 hero-right">
                                    <div class="img-container">
                                        <div class="swiper-slide-content">
                                            <img src="/images/hero/saas2.jpg" alt="" class="img-fluid rounded-lg"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Slide 3 --}}
                        <div class="swiper-slide">
                            <div class="row align-items-center text-center text-sm-start">
                                <div class="col-lg-6">
                                    <div class="mb-lg-0 mb-5">
                                        <h1 class="hero-title">
                                            {!! __('saas.hero_slide3_title') !!}
                                        </h1>
                                        <p class="fs-17 text-muted pt-3">
                                            {!! __('saas.hero_slide3_lead') !!}
                                        </p>
                                        <div class="pt-5">
                                            <div class="row g-2 text-start">
                                                <div class="col-md-4 col-lg-6">
                                                    <label class="visually-hidden" for="email3">Email</label>
                                                    <input type="email" class="form-control mb-2 me-sm-2 shadow-sm" name="email"
                                                           id="email3" placeholder="{{ __('saas.email_placeholder') }}">
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="submit" class="btn btn-primary mb-2">{{ __('saas.try_it') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mt-1 fs-12">
                                            <div class="me-4">
                                                <i data-feather="check" class="icon icon-dual-success icon-xs me-1"></i>{{ __('saas.demo_free') }}
                                            </div>
                                            <div>
                                                <i data-feather="check" class="icon icon-dual-success icon-xs me-1"></i>{{ __('saas.no_credit_card') }}
                                            </div>
                                        </div>
                                        <div class="pt-5">
                                            <ul class="navbar-nav align-items-lg-center d-flex">
                                                <li class="nav-item ms-2">
                                                    <a class="btn btn-primary btn-sm" href="{{ route('pages.pricing') }}">{{ __('saas.view_plans') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 offset-lg-1 hero-right">
                                    <div class="img-container">
                                        <div class="swiper-slide-content">
                                            <img src="/images/hero/saas3.jpg" alt="" class="img-fluid rounded-lg"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination hero-swiper-pagination mt-3"></div>
            </div>
        </div>
    </section>

    <!-- features start -->
    <section class="position-relative overflow-hidden pt-lg-6 py-4 pb-lg-7">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('saas.features_badge') }}</span>
                    <h1 class="display-5 fw-medium">{{ __('saas.features_title') }}</h1>
                    <p class="text-muted mx-auto">
                        {!! __('saas.features_subtitle') !!}
                    </p>
                </div>
            </div>

            <div class="row pt-5 align-items-center features-3">
                <div class="col-lg-6">
                    <div class="img-content position-relative">
                        <div class="img-up mb-lg-0 mb-6">
                            <img src="/images/hero/saas1-{{ app()->getLocale() }}.png" alt="app img" class="img-fluid d-block rounded"
                                 data-aos="fade-right" data-aos-duration="200"/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1">
                    <div class="accordion" id="features-list" data-aos="fade-up" data-aos-duration="300">
                        <div class="d-flex border-bottom pb-4">
                            <span class="bg-soft-primary avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-primary me-3 flex-shrink-0">
                                @svg('/duotone-icons/communication/Group')
                            </span>
                            <div class="flex-grow-1">
                                <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#feature1ex" role="button" aria-expanded="false" aria-controls="feature1ex">{{ __('saas.feature_item_1_title') }}</a>
                                <div class="collapse show" id="feature1ex" data-bs-parent="#features-list">
                                    <p class="text-muted mt-1 mb-0">{{ __('saas.feature_item_1_desc') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex border-bottom py-4">
                            <span class="bg-soft-success avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-success me-3 flex-shrink-0">
                                @svg('/duotone-icons/communication/Add-user')
                            </span>
                            <div class="flex-grow-1">
                                <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#feature2ex" role="button" aria-expanded="false" aria-controls="feature2ex">{{ __('saas.feature_item_2_title') }}</a>
                                <div class="collapse" id="feature2ex" data-bs-parent="#features-list">
                                    <p class="text-muted mt-1 mb-0">{{ __('saas.feature_item_2_desc') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex border-bottom py-4">
                            <span class="bg-soft-orange avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-orange me-3 flex-shrink-0">
                                @svg('/duotone-icons/shopping/Chart-bar#3')
                            </span>
                            <div class="flex-grow-1">
                                <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#feature3ex" role="button" aria-expanded="false" aria-controls="feature3ex">{{ __('saas.feature_item_3_title') }}</a>
                                <div class="collapse" id="feature3ex" data-bs-parent="#features-list">
                                    <p class="text-muted mt-1 mb-0">{{ __('saas.feature_item_3_desc') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex border-bottom py-4">
                            <span class="bg-soft-primary avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-primary me-3 flex-shrink-0">
                                @svg('/duotone-icons/shopping/MC')
                            </span>
                            <div class="flex-grow-1">
                                <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#feature4ex" role="button" aria-expanded="false" aria-controls="feature4ex">{{ __('saas.feature_item_4_title') }}</a>
                                <div class="collapse" id="feature4ex" data-bs-parent="#features-list">
                                    <p class="text-muted mt-1 mb-0">{{ __('saas.feature_item_4_desc') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex border-bottom py-4">
                            <span class="bg-soft-success avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-success me-3 flex-shrink-0">
                                @svg('/duotone-icons/communication/Add-user')
                            </span>
                            <div class="flex-grow-1">
                                <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#feature5ex" role="button" aria-expanded="false" aria-controls="feature5ex">{{ __('saas.feature_item_5_title') }}</a>
                                <div class="collapse" id="feature5ex" data-bs-parent="#features-list">
                                    <p class="text-muted mt-1 mb-0">{{ __('saas.feature_item_5_desc') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 mt-2">
                            <a href="{{ route('pages.digitalizar-operador-turistico') }}" class="d-inline-flex align-items-center gap-2 fw-semibold text-primary text-decoration-none py-2 px-3 rounded bg-soft-primary">
                                {{ __('saas.features_cta') }}
                                <i class="icon-xs" data-feather="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="position-relative overflow-hidden py-7 features-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="mb-lg-0 mb-5" data-aos="fade-right" data-aos-duration="200">
                        <h1 class="text-dark mb-4">{{ __('saas.smart_payroll_title') }}</h1>
                        <div class="accordion" id="smart-section-list">
                            <div class="d-flex border-bottom pb-4">
                                <span class="bg-soft-primary avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-primary me-3 flex-shrink-0">@svg('/duotone-icons/communication/Group')</span>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#smart1ex" role="button" aria-expanded="false" aria-controls="smart1ex">{{ __('saas.smart_item_1_title') }}</a>
                                    <div class="collapse show" id="smart1ex" data-bs-parent="#smart-section-list">
                                        <p class="text-muted mt-1 mb-0">{{ __('saas.smart_item_1_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex border-bottom py-4">
                                <span class="bg-soft-success avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-success me-3 flex-shrink-0">@svg('/duotone-icons/communication/Add-user')</span>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#smart2ex" role="button" aria-expanded="false" aria-controls="smart2ex">{{ __('saas.smart_item_2_title') }}</a>
                                    <div class="collapse" id="smart2ex" data-bs-parent="#smart-section-list">
                                        <p class="text-muted mt-1 mb-0">{{ __('saas.smart_item_2_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex border-bottom py-4">
                                <span class="bg-soft-orange avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-orange me-3 flex-shrink-0">@svg('/duotone-icons/shopping/Chart-bar#3')</span>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#smart3ex" role="button" aria-expanded="false" aria-controls="smart3ex">{{ __('saas.smart_item_3_title') }}</a>
                                    <div class="collapse" id="smart3ex" data-bs-parent="#smart-section-list">
                                        <p class="text-muted mt-1 mb-0">{{ __('saas.smart_item_3_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex border-bottom py-4">
                                <span class="bg-soft-primary avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-primary me-3 flex-shrink-0">@svg('/duotone-icons/shopping/MC')</span>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#smart4ex" role="button" aria-expanded="false" aria-controls="smart4ex">{{ __('saas.smart_item_4_title') }}</a>
                                    <div class="collapse" id="smart4ex" data-bs-parent="#smart-section-list">
                                        <p class="text-muted mt-1 mb-0">{{ __('saas.smart_item_4_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex pt-4">
                                <span class="bg-soft-success avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-success me-3 flex-shrink-0">@svg('/duotone-icons/communication/Add-user')</span>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-dark h4" data-bs-toggle="collapse" data-bs-target="#smart5ex" role="button" aria-expanded="false" aria-controls="smart5ex">{{ __('saas.smart_item_5_title') }}</a>
                                    <div class="collapse" id="smart5ex" data-bs-parent="#smart-section-list">
                                        <p class="text-muted mt-1 mb-0">{{ __('saas.smart_item_5_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-2 order-1">
                    <div class="img-content2 position-relative" data-aos="fade-left" data-aos-duration="300">
                        <div class="img-up">
                            <img src="/images/hero/saas2.jpg" alt="app img" class="img-fluid d-block rounded"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="position-relative pb-6 pt-lg-6 pt-4 features-3">
        <div class="container" data-aos="fade-up" data-aos-duration="200">
            <div class="row justify-content-center">
                <div class="col text-center">
                    <h3 class="fw-medium mb-5">{{ __('saas.more_features_title') }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_hire_retain') }}</h6>
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_team_mgmt') }}</h6>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_stay_compliant') }}</h6>
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_improve_productivity') }}</h6>
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_improve_experience') }}</h6>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_time_tracking') }}</h6>
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_performance_mgmt') }}</h6>
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_expert_hr') }}</h6>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_new_hire_checklist') }}</h6>
                    <h6 class="fw-medium fs-16 mb-4"><i class="icon-sm icon-dual-success me-2" data-feather="check"></i>{{ __('saas.feature_tax_calculator') }}</h6>
                </div>
            </div>
            <div class="row mt-5 justify-content-center">
                <div class="col-auto">
                    <a href="#" class="btn btn-primary mb-2">
                        {{ __('saas.sign_up_now') }} <i class="icon-xs ms-2" data-feather="arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- features end -->

    <!-- clients start -->
    <section class="section pt-8 pb-6 bg-gradient3 position-relative">
        <div class="divider top d-none d-sm-block"></div>
        <div class="container">
            <div class="row" data-aos="fade-up" data-aos-duration="200">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('saas.clients_badge') }}</span>
                    <h1 class="display-5 fw-medium">{{ __('saas.clients_title') }}</h1>
                    <p class="text-muted mx-auto">
                        {!! __('saas.clients_subtitle') !!}
                    </p>

                    <ul class="list-inline mt-5">
                        <li class="list-inline-item mx-4 mx-xl-5 mb-3">
                            <img src="/images/brands/amazon.svg" alt="" height="32"/>
                        </li>
                        <li class="list-inline-item mx-4 mx-xl-5 mb-3">
                            <img src="/images/brands/google.svg" alt="" height="32"/>
                        </li>
                        <li class="list-inline-item mx-4 mx-xl-5 mb-3">
                            <img src="/images/brands/paypal.svg" alt="" height="32"/>
                        </li>
                        <li class="list-inline-item mx-4 mx-xl-5 mb-3">
                            <img src="/images/brands/spotify.svg" alt="" height="32"/>
                        </li>
                        <li class="list-inline-item mx-4 mx-xl-5 mb-3">
                            <img src="/images/brands/shopify.svg" alt="" height="32"/>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- client ends -->

    <!-- testimonials start -->
    <section class="section pt-5 pb-7 py-sm-9 position-relative features-4">
        <div class="container">
            <div class="row testimonials-2" data-aos="fade-up" data-aos-duration="200">
                <div class="col-lg-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('saas.testimonials_badge') }}</span>
                            <h1 class="display-5 fw-medium">{{ __('saas.testimonials_title') }}</h1>
                            <p class="text-muted mx-auto">{{ __('saas.testimonials_subtitle') }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-auto text-sm-right pt-2 pt-sm-0">
                            <div class="navigations mb-4 mb-lg-0">
                                <button class="btn btn-link text-normal p-0 testimonial-prev" type="button" aria-label="Anterior">
                                    <svg class="bi bi-arrow-left" width="1.75em" height="1.75em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M5.854 4.646a.5.5 0 010 .708L3.207 8l2.647 2.646a.5.5 0 01-.708.708l-3-3a.5.5 0 010-.708l3-3a.5.5 0 01.708 0z"
                                              clip-rule="evenodd"></path>
                                        <path fill-rule="evenodd"
                                              d="M2.5 8a.5.5 0 01.5-.5h10.5a.5.5 0 010 1H3a.5.5 0 01-.5-.5z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <button class="btn btn-link text-normal p-0 testimonial-next" type="button" aria-label="Siguiente">
                                    <svg class="bi bi-arrow-right" width="1.75em" height="1.75em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M10.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L12.793 8l-2.647-2.646a.5.5 0 010-.708z"
                                              clip-rule="evenodd"></path>
                                        <path fill-rule="evenodd"
                                              d="M2 8a.5.5 0 01.5-.5H13a.5.5 0 010 1H2.5A.5.5 0 012 8z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <div class="slider">
                        <div class="swiper testimonial-swiper-saas">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="card mb-0 border rounded">
                                        <div class="card-body testimonial-body shadow">
                                            <p class="quotation-mark text-muted mb-0">“</p>
                                            <h4 class="fw-normal mb-3 mt-0">
                                                {{ __('saas.testimonial1_quote') }}
                                            </h4>
                                            <hr/>
                                            <div class="d-flex pt-2">
                                                <img class="me-2 flex-shrink-0 rounded-circle"
                                                     src="/images/avatars/img-8.jpg" alt="" height="36"/>
                                                <div class="flex-grow-1">
                                                    <h6 class="m-0">{{ __('saas.testimonial1_author') }}</h6>
                                                    <p class="my-0 text-muted fs-13">{{ __('saas.testimonial1_role') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="card mb-0 border rounded">
                                        <div class="card-body testimonial-body shadow">
                                            <p class="quotation-mark text-muted mb-0">“</p>
                                            <h4 class="fw-normal mb-3 mt-0">
                                                {{ __('saas.testimonial2_quote') }}
                                            </h4>
                                            <hr/>
                                            <div class="d-flex pt-2 text-align-start">
                                                <img class="me-2 flex-shrink-0 rounded-circle"
                                                     src="/images/avatars/img-5.jpg" alt="" height="36"/>
                                                <div class="flex-grow-1">
                                                    <h6 class="m-0">{{ __('saas.testimonial2_author') }}</h6>
                                                    <p class="my-0 text-muted fs-13">{{ __('saas.testimonial2_role') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="card mb-0 border rounded">
                                        <div class="card-body testimonial-body shadow">
                                            <p class="quotation-mark text-muted mb-0">“</p>
                                            <h4 class="fw-normal mb-3 mt-0">
                                                {{ __('saas.testimonial1_quote') }}
                                            </h4>
                                            <hr/>
                                            <div class="d-flex pt-2">
                                                <img class="me-2 flex-shrink-0 rounded-circle"
                                                     src="/images/avatars/img-8.jpg" alt="" height="36"/>
                                                <div class="flex-grow-1">
                                                    <h6 class="m-0">{{ __('saas.testimonial1_author') }}</h6>
                                                    <p class="my-0 text-muted fs-13">{{ __('saas.testimonial1_role') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonials end -->

    <!-- pricing start -->
    <section class="section py-6 py-sm-8 bg-gradient3 position-relative">
        <div class="divider top d-none d-sm-block"></div>
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('saas.plans_badge') }}</span>
                    <h1 class="display-5 fw-medium">{{ __('saas.plans_title') }}</h1>
                    <p class="text-muted mx-auto">
                        {!! __('saas.plans_subtitle') !!}</p>
                </div>
            </div>

            <div class="row align-items-center mt-0 mt-sm-5">
                <div class="col-lg-4 col-xl-4">
                    <div class="card border hoverable" data-aos="fade-up" data-aos-duration="500">
                        <div class="card-body text-center">
                            <h4 class="my-0 text-primary">{{ __('saas.plan_starter') }}</h4>
                            <h1 class="mb-0">
                                <span class="fw-normal text-muted fs-13 align-top">$</span>
                                <span class="fw-bolder display-4">49</span>
                                <span class="fw-normal text-muted fs-13 align-middle"> {{ __('saas.per_month') }}</span>
                            </h1>

                            <ul class="list-unstyled border-top py-4 mt-4 text-start">
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_600_min') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_personal_only') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_10_attendees') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_email_support') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    &nbsp;
                                </li>
                            </ul>
                            <a href="#" class="btn btn-soft-primary d-block">{{ __('saas.sign_up_now') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-xl-4">
                    <div class="card border hoverable" data-aos="fade-up" data-aos-duration="700">
                        <div class="card-body text-center">
                            <h4 class="my-0 text-primary">{{ __('saas.plan_professional') }}</h4>
                            <h1 class="mb-0">
                                <span class="fw-normal text-muted fs-13 align-top">$</span>
                                <span class="fw-bolder display-4">99</span>
                                <span class="fw-normal text-muted fs-13 align-middle"> {{ __('saas.per_month') }}</span>
                            </h1>

                            <ul class="list-unstyled border-top py-4 mt-4 text-start">
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_6000_min') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_personal_commercial') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_100_attendees') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_5_teams') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_email_support') }}</span>
                                </li>
                            </ul>
                            <a href="#" class="btn btn-primary d-block">{{ __('saas.sign_up_now') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-xl-4">
                    <div class="card border hoverable" data-aos="fade-up" data-aos-duration="900">
                        <div class="card-body text-center">
                            <h4 class="my-0 text-primary">{{ __('saas.plan_enterprise') }}</h4>
                            <h1 class="mb-0">
                                <span class="fw-normal text-muted fs-13 align-top">$</span>
                                <span class="fw-bolder display-4">599</span>
                                <span class="fw-normal text-muted fs-13 align-middle"> {{ __('saas.per_month') }}</span>
                            </h1>

                            <ul class="list-unstyled border-top py-4 mt-4 text-start">
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_unlimited') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_personal_commercial') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_unlimited_attendees') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_24x7_support') }}</span>
                                </li>
                                <li class="py-2 d-flex flex-row align-items-center">
                                    <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                    <span>{{ __('saas.plan_email_support') }}</span>
                                </li>
                            </ul>
                            <a href="#" class="btn btn-soft-primary d-block">{{ __('saas.sign_up_now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="divider bottom d-none d-sm-block"></div>
    </section>
    <!-- pricing end -->

    <!-- faq start -->
    <section class="section py-6 pt-sm-6 position-relative">
        <div class="container" data-aos="fade-up" data-aos-duration="2000">
            <div class="row">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('saas.faq_badge') }}</span>
                    <h1 class="display-5 fw-medium">{{ __('saas.faq_title') }}</h1>
                    <p class="text-muted mx-auto">
                        {{ __('saas.faq_subtitle') }}
                    </p>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-10 col-lg-8">
                    <div id="faqContent">
                        <div class="accordion custom-accordionwitharrow" id="accordionExample">
                            <div class="card mb-2 border rounded-sm">
                                <a href="" class="text-dark" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                   aria-expanded="true" aria-controls="collapseOne">
                                    <div class="card-header" id="headingOne">
                                        <h5 class="my-1 fw-medium">{{ __('saas.faq1_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('saas.faq1_a') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-2 border rounded-sm">
                                <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                   data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <div class="card-header" id="headingTwo">
                                        <h5 class="my-1 fw-medium">{{ __('saas.faq2_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('saas.faq2_a') }}
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-2 border rounded-sm">
                                <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                   data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <div class="card-header" id="headingThree">
                                        <h5 class="my-1 fw-medium">{{ __('saas.faq3_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('saas.faq3_a') }}
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-2 border rounded-sm">
                                <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                   data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="my-1 fw-medium">{{ __('saas.faq4_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('saas.faq4_a') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mt-5">
                <div class="col-auto">
                    <div class="rounded d-inline-block py-2 px-3 alert bg-light">
                        <div class="align-items-center">
                            <div class="text-dark">
                                {{ __('saas.still_have_questions') }} <a href="">{{ __('saas.contact_us') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- faq end -->

    <x-site-footer />

@endsection


