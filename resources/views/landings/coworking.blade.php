@extends('layouts.base', ['title' => 'Prompt | Co-Working Space Landing Page'])

@section('content')

    <div class="header-5">

        @include('layouts.partials.navbar', ['hideSearch' => true,'fixedWidth' => true, 'sticky' => true, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto','ctaButtonClass' => 'btn-outline-orange btn-sm' ])

        <section class="hero-2">
            <div class="container py-3 py-sm-6">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1 class="hero-title mt-0">Explore the best coworking space in the heart of the City</h1>
                    </div>
                    <div class="col-lg-5">
                        <p class="fs-17 ps-0 ps-sm-4">No more conventional four-walled office. The fully comformtable
                            seating solution for you.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="slider pt-3 pt-sm-5 mt-5">
                            <!-- slider -->
                            <div class="form-container">
                                <div class="row align-items-top px-3 px-sm-5">
                                    <div class="col-lg-12">
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-sm-auto ">
                                                                <h5 class="mt-0 fw-medium my-1 my-sm-0 pe-3">Search your
                                                                    perfect space</h5>
                                                            </div>
                                                            <div class="col-sm-auto">
                                                                <label class="visually-hidden"
                                                                       for="location">location</label>
                                                                <div class="form-control-with-hint me-sm-2">
                                                                    <input type="text" class="form-control"
                                                                           id="location" placeholder="Enter location">
                                                                    <span
                                                                        class="form-control-feedback uil uil-location-pin-alt fs-18"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-auto">
                                                                <button type="submit"
                                                                        class="btn btn-orange my-1 my-sm-0">Find Space
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-auto text-sm-end pt-2 pt-sm-0">
                                                        <div class="navigations">
                                                            <button class="btn btn-info swiper-custom-prev">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                     height="24" viewBox="0 0 24 24" fill="none"
                                                                     stroke="white" stroke-width="1.5"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-chevron-left">
                                                                    <polyline points="15 18 9 12 15 6"></polyline>
                                                                </svg>
                                                            </button>
                                                            <button class="btn btn-info swiper-custom-next">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                     height="24" viewBox="0 0 24 24" fill="none"
                                                                     stroke="white" stroke-width="1.5"
                                                                     stroke-linecap="round" stroke-linejoin="round"
                                                                     class="feather feather-chevron-right">
                                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper effect-fade-swiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="slider-item"
                                             style="background-image: url('/images/hero/coworking2.jpg');"></div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="slider-item"
                                             style="background-image: url('/images/hero/coworking3.jpg');"></div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="slider-item"
                                             style="background-image: url('/images/hero/coworking4.jpg');"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="shape bottom d-none d-sm-block">
                <svg width="1440px" height="40px" viewBox="0 0 1440 40" version="1.1" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="shape-b" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="curve" fill="#fff">
                            <path
                                d="M0,30.013 C239.659,10.004 479.143,0 718.453,0 C957.763,0 1198.28,10.004 1440,30.013 L1440,40 L0,40 L0,30.013 Z"
                                id="Path"></path>
                        </g>
                    </g>
                </svg>
            </div>
        </section>
    </div>

    <!-- about start -->
    <section class="py-lg-6 py-4 mt-xl-10 mt-0 coworking-1">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <div class="text-center">
                        <span class="badge rounded-pill badge-soft-info px-2 py-1">About</span>
                        <h1 class="display-5 fw-semibold">More Productivity, Less Expenses</h1>
                        <p class="text-muted mx-auto w-75 mt-1">From an established enterprise or a startup, we offer
                            space that fits all.</p>

                        <div class="row mt-5 text-center" data-aos="fade-up">
                            <div class="col-6 col-md-3 mb-5 mb-sm-0">
                                <div class="display-3 fw-bold" data-toggle="counter" data-from="0" data-to="21"
                                     data-decimals="0" data-duration="3" data-options='{}'>0
                                </div>
                                <p class="mt-1 mb-0">Meeting Rooms</p>
                            </div>

                            <div class="col-6 col-md-3 mb-5 mb-sm-0">
                                <div class="display-3 fw-bold" data-toggle="counter" data-from="5" data-to="51"
                                     data-decimals="0" data-duration="3" data-options='{}'>51
                                </div>
                                <p class="mt-1 mb-0">Event Spaces</p>
                            </div>

                            <div class="col-6 col-md-3 mb-5 mb-sm-0">
                                <div class="display-3 fw-bold" data-toggle="counter" data-from="1" data-to="11"
                                     data-decimals="0" data-duration="3" data-options='{}'>11
                                </div>
                                <p class="mt-1 mb-0">Studio Rooms</p>
                            </div>

                            <div class="col-6 col-md-3 mb-5 mb-sm-0">
                                <div class="display-3 fw-bold" data-toggle="counter" data-from="100" data-to="500"
                                     data-decimals="0" data-duration="3" data-options='{"suffix": "+"}'></div>
                                <p class="mt-1 mb-0">Seating Spaces</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about end -->

    <!-- features start -->
    <section class="my-lg-5 py-5 py-sm-7 bg-gradient5 position-relative" data-aos="fade-up">
        <div class="divider top d-none d-sm-block"></div>
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-orange px-2 py-1">Features</span>
                    <h1 class="display-5 fw-semibold">Why Choose Us</h1>
                    <p class="text-secondary mx-auto">The benefits that will make you comfort</p>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex text-align-start">
                                <span
                                    class="bg-soft-orange avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-orange me-4 flex-shrink-0">
                                    @svg('/duotone-icons/devices/Router#2')
                                </span>

                                <div class="flex-grow-1">
                                    <h5 class="mt-0">High-Speed Wireless</h5>
                                    <p class="mb-0">
                                        We've watched Bootstrap grow up over the years and understand it better than
                                        almost anyone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex text-align-start">
                                <span
                                    class="bg-soft-orange avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-orange me-4 flex-shrink-0">
                                    @svg('/duotone-icons/communication/Group')
                                </span>
                                <div class="flex-grow-1">
                                    <h5 class="mt-0">Community Events</h5>
                                    <p class="mb-0">
                                        You have a business to run. Stop worring about cross-browser keeping your
                                        components up to date.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex text-align-start">
                                <span
                                    class="bg-soft-orange avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-orange me-4 flex-shrink-0">
                                    @svg('/duotone-icons/home/Armchair')
                                </span>
                                <div class="flex-grow-1">
                                    <h5 class="mt-0">Exercise Facilities</h5>
                                    <p class="mb-0">
                                        Replacing a maintains the amount of lines. When replacing a selection objectives
                                        and then create.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex text-align-start">
                                <span
                                    class="bg-soft-orange avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-orange me-4 flex-shrink-0">
                                    @svg('/duotone-icons/home/Couch')
                                </span>
                                <div class="flex-grow-1">
                                    <h5 class="mt-0">Comfortable Lounges</h5>
                                    <p class="mb-0">
                                        Risus sed vulputate odio ut enim blandit. Malesuada consequat interdum mattis
                                        facilisis.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- features end -->

    <!-- Options start -->
    <section class="py-5 position-relative">
        <div class="container">
            <div class="row" data-aos="fade-up">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-orange px-2 py-1">Flexible</span>
                    <h1 class="display-5 fw-medium">Coworking Space Options</h1>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-6 col-xl-4">
                    <div class="card shadow-lg rounded" data-aos="fade-up" data-aos-duration="600">
                        <img src="/images/photos/8.jpg" alt="" class="card-img-top"/>
                        <div class="card-body">
                            <div class="">
                                <h4 class="mt-0"><a href="#" class="text-orange">Shared Desk</a></h4>
                                <p class="text-muted mb-2">
                                    Access to shared workspace and conference rooms. Most suitable to individual looking
                                    for people's company.
                                </p>
                            </div>
                            <div class="pt-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <p class="mb-0">
                                            <i data-feather="user" class="icon icon-dual icon-xs me-1"></i>
                                            <a href="" class="fs-13 align-middle text-muted">1-5 Shared Spaces</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4">
                    <div class="card shadow-lg rounded" data-aos="fade-up" data-aos-duration="1000">
                        <img src="/images/photos/5.jpg" alt="" class="card-img-top"/>
                        <div class="card-body">
                            <div class="">
                                <h4 class="mt-0"><a href="#" class="text-orange">Dedicated Desk</a></h4>
                                <p class="text-muted mb-2">
                                    A dedicated desk space for you, with 24/7 access to premium amenities and conference
                                    rooms.
                                </p>
                            </div>
                            <div class="pt-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <p class="mb-0">
                                            <i data-feather="user" class="icon icon-dual icon-xs me-1"></i>
                                            <a href="" class="fs-13 align-middle text-muted">1-5 Dedicated Spaces</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4">
                    <div class="card shadow-lg rounded" data-aos="fade-up" data-aos-duration="1400">
                        <img src="/images/photos/4.jpg" alt="" class="card-img-top"/>
                        <div class="card-body">
                            <div class="">
                                <h4 class="mt-0"><a href="#" class="text-orange">Event Space</a></h4>
                                <p class="text-muted mb-2">
                                    An excluisive venue designed specifically for events of all kinds, from conferences
                                    to celebrations.
                                </p>
                            </div>
                            <div class="pt-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <p class="mb-0">
                                            <i data-feather="users" class="icon icon-dual icon-xs me-1"></i>
                                            <a href="" class="fs-13 align-middle text-muted">Upto 200 People</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Options end -->

    <!-- testimonials start -->
    <section class="section py-4 py-sm-7 position-relative overflow-hidden" data-aos="fade-up">
        <div class="container testimonials-3">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="display-5 fw-medium">See what our members are saying</h1>
                </div>
                <div class="col-auto text-sm-end pt-2 pt-sm-0">
                    <div class="navigations">
                        <button class="btn btn-link text-orange p-0 swiper-custom-prev">
                            <svg class="bi bi-arrow-left" width="1.75em" height="1.75em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M5.854 4.646a.5.5 0 010 .708L3.207 8l2.647 2.646a.5.5 0 01-.708.708l-3-3a.5.5 0 010-.708l3-3a.5.5 0 01.708 0z"
                                      clip-rule="evenodd"></path>
                                <path fill-rule="evenodd" d="M2.5 8a.5.5 0 01.5-.5h10.5a.5.5 0 010 1H3a.5.5 0 01-.5-.5z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <button class="btn btn-link text-orange p-0 swiper-custom-next">
                            <svg class="bi bi-arrow-right" width="1.75em" height="1.75em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M10.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L12.793 8l-2.647-2.646a.5.5 0 010-.708z"
                                      clip-rule="evenodd"></path>
                                <path fill-rule="evenodd" d="M2 8a.5.5 0 01.5-.5H13a.5.5 0 010 1H2.5A.5.5 0 012 8z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-3 mt-sm-5">
                <div class="col">
                    <div class="swiper testimonial-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="card mb-0 shadow border">
                                    <div class="card-body p-md-5">
                                        <h5 class="fw-normal mb-4 mt-0">
                                            Great office and great location. Worth the money if it makes sense for
                                            your business.
                                        </h5>
                                        <div class="d-flex text-align-start">
                                            <img class="me-2 rounded-circle" src="/images/avatars/img-8.jpg"
                                                 alt="" height="36"/>
                                            <div class="flex-grow-1">
                                                <h6 class="m-0">Cersei Lannister</h6>
                                                <p class="my-0 text-muted fs-13">Senior Project Manager</p>
                                            </div>
                                            <img class="" src="/images/brands/google.svg" alt="" height="32"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="card mb-0 border">
                                    <div class="card-body p-md-5">
                                        <h5 class="fw-normal mb-4 mt-0">
                                            Awesome vibe and great staff! Top cooworking spots in the city! Loved to
                                            be here!
                                        </h5>
                                        <div class="d-flex text-align-start">
                                            <img class="me-2 rounded-circle" src="/images/avatars/img-5.jpg"
                                                 alt="" height="36"/>
                                            <div class="flex-grow-1">
                                                <h6 class="m-0">John Stark</h6>
                                                <p class="my-0 text-muted fs-13">Engineering Director</p>
                                            </div>
                                            <img class="" src="/images/brands/amazon.svg" alt="" height="32"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="card mb-0 shadow border">
                                    <div class="card-body p-md-5">
                                        <h5 class="fw-normal mb-4 mt-0">
                                            Great office and great location. Worth the money if it makes sense for
                                            your business.
                                        </h5>
                                        <div class="d-flex text-align-start">
                                            <img class="me-2 rounded-circle" src="/images/avatars/img-8.jpg"
                                                 alt="" height="36"/>
                                            <div class="flex-grow-1">
                                                <h6 class="m-0">Cersei Lannister</h6>
                                                <p class="my-0 text-muted fs-13">Senior Project Manager</p>
                                            </div>
                                            <img class="" src="/images/brands/google.svg" alt="" height="32"/>
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


    <!-- footer start -->
    <section class="py-5 py-sm-6 bg-gradient5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                        <x-site-logo class="navbar-brand me-lg-4 me-auto pt-0" :url="'#'" />
                    <div class="">
                        <p class="mt-3 mb-1 text-dark">At vero eos et accusamus et iusto dignissimos ducimus odio.</p>
                        <p class="mt-lg-5 pt-4 mb-lg-0 mb-4 text-dark">Prompt 2020. All rights reserved.</p>
                    </div>
                </div>

                <div class="col-lg-7 offset-lg-1">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <h6 class="mb-4 mt-4 mt-sm-2 text-dark fw-semibold text-uppercase">Navigations</h6>
                            <ul class="list-unstyled">
                                <li class="my-3"><a href="#" class="text-dark">Home</a></li>
                                <li class="my-3"><a href="#" class="text-dark">Locations</a></li>
                                <li class="my-3"><a href="#" class="text-dark">Plans</a></li>
                                <li class="my-3"><a href="#" class="text-dark">Events</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <h6 class="mb-4 mt-4 mt-sm-2 text-dark fw-semibold text-uppercase">Contact</h6>
                            <ul class="list-unstyled">
                                <li class="my-3"><a href="#" class="text-dark">Support</a></li>
                                <li class="my-3"><a href="#" class="text-dark">Developers</a></li>
                                <li class="my-3"><a href="#" class="text-dark">Customer Service</a></li>
                                <li class="my-3"><a href="#" class="text-dark">Get Started Guide</a></li>
                            </ul>
                        </div>
                        <div class="col-md-5 offset-md-1">
                            <h6 class="mb-4 mt-4 mt-sm-2 text-dark fw-semibold text-uppercase">Subscribe To
                                Newsletters</h6>
                            <div class="input-group my-3">
                                <input type="text" class="form-control h-auto" placeholder="Your email"
                                       aria-label="keywords"/>
                                <a href="#" class="btn btn-secondary input-group-text"><i class="icon-xs"
                                                                                          data-feather="mail"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- footer end -->
@endsection


