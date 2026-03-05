@extends('layouts.base', ['title' => 'Prompt | UI Kit'])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true,'fixedWidth' => true, 'sticky' => true,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto','ctaButtonClass' => 'btn-outline-primary btn-sm' ])

    <section class="py-4 bg-light">
        <div class="container">
            <!-- AoS Start -->
            <div class="row" id="aos">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-0">AoS</h5>
                            <p class="sub-header">
                                Animate on scroll library.
                            </p>

                            <div class="py-3">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6">
                                        <div class="border rounded p-3 mb-2 mb-xl-0" data-aos="fade-up"
                                             data-aos-duration="1000">
                                            <span
                                                class="bg-soft-primary avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-primary">
                                                @svg('/duotone-icons/communication/Mail-opened')
                                            </span>

                                            <h4 class="mt-3 mb-2 fw-semibold">First feature</h4>
                                            <p class="text-muted">A itaque earum rerum a tenetur the sapiente
                                                delectus aut reiciendis alias omnis natus.</p>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="border rounded p-3 mb-2 mb-xl-0" data-aos="fade-left"
                                             data-aos-duration="2000">
                                            <span
                                                class="bg-soft-primary avatar avatar-sm rounded-lg icon icon-with-bg icon-xs text-primary">
                                                @svg('/duotone-icons/communication/Mail-opened')
                                            </span>

                                            <h4 class="mt-3 mb-2 fw-semibold">First feature</h4>
                                            <p class="text-muted">A itaque earum rerum a tenetur the sapiente
                                                delectus aut reiciendis alias omnis natus.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="code-block mt-2">
                                <h6>Example</h6>

                                <button class="btn btn-sm btn-copy-clipboard" data-clipboard-target="#aos-ex-1">
                                    Copy
                                </button>
                                <pre class="prettyprint lang-html escape" id="aos-ex-1">
                                            <div data-aos="fade-left" data-aos-duration="2000">...</div>
                                        </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- AoS End -->

            <!-- countup Start -->
            <div class="row" id="countup">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-0">Count Up</h5>
                            <p class="sub-header">
                                CountUp.js is a dependency-free, lightweight JavaScript class that can be used to
                                quickly create animations that display numerical data in a more interesting way.
                            </p>

                            <div class="py-3">
                                <div class="row text-center">
                                    <div class="col-xl-3 col-md-6 mb-4 mb-sm-0">
                                        <div class="display-4 fw-light">
                                            <span data-toggle="counter" data-from="10" data-to="100" data-decimals="0"
                                                  data-duration="5" data-options='{}'></span>+
                                        </div>
                                        <p class="mt-2 mb-0 fw-semibold">
                                            Products Built
                                        </p>
                                        <p>helped clients across the globe</p>
                                    </div>

                                    <div class="col-xl-3 col-md-6 mb-4 mb-sm-0">
                                        <div class="display-4 fw-light">
                                            $<span data-toggle="counter" data-from="5" data-to="21" data-decimals="0"
                                                   data-duration="5" data-options='{}'></span>M+
                                        </div>
                                        <p class="mt-2 mb-0 fw-semibold">
                                            Revenue Generated
                                        </p>
                                        <p>across 10+ countries</p>
                                    </div>

                                    <div class="col-xl-3 col-md-6 mb-4 mb-sm-0">
                                        <div class="display-4 fw-light">
                                            <span data-toggle="counter" data-from="10" data-to="100" data-decimals="0"
                                                  data-duration="5" data-options='{}'></span>+
                                        </div>
                                        <p class="mt-2 mb-0 fw-semibold">
                                            Satisfied Clients
                                        </p>
                                        <p>across 100+ locations</p>
                                    </div>

                                    <div class="col-xl-3 col-md-6 mb-4 mb-sm-0">
                                        <div class="display-4 fw-light">
                                            <span data-toggle="counter" data-from="1" data-to="10" data-decimals="0"
                                                  data-duration="5" data-options='{}'></span>+
                                        </div>
                                        <p class="mt-2 mb-0 fw-semibold">
                                            Awards Won
                                        </p>
                                        <p>on Awwwards, CSS Design Awards</p>
                                    </div>
                                </div>
                            </div>

                            <div class="code-block mt-2">
                                <h6>Example</h6>

                                <button class="btn btn-sm btn-copy-clipboard" data-clipboard-target="#counter-ex-1">
                                    Copy
                                </button>
                                <pre class="prettyprint lang-html escape" id="counter-ex-1">
                                            <div data-toggle="counter" data-from="5" data-to="21" data-decimals="0"
                                                 data-duration="5" data-options='{}'>...</div>
                                        </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- countup End -->

            <!-- custom-jarallax start -->
            <div class="row" id="jarallax">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-0">Just Another Parallax</h5>
                            <p class="sub-header">
                                Smooth parallax scrolling effect for background images, videos. Code in pure JavaScript
                                with NO dependencies + jQuery supported. YouTube, Vimeo and Self-Hosted Videos parallax
                                supported.
                            </p>

                            <div class="py-3">
                                <div class="row text-center">
                                    <div class="col-lg-12">
                                        <div class="position-relative">
                                            <div class="jarallax" data-jarallax data-speed=".2"
                                                 style="background-image: url(/images/hero/coworking2.jpg); height: 320px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="code-block mt-2">
                                <h6>Example</h6>

                                <button class="btn btn-sm btn-copy-clipboard" data-clipboard-target="#counter-ex-1">
                                    Copy
                                </button>
                                <pre class="prettyprint lang-html escape" id="counter-ex-1">
                                            <div class="jarallax" data-jarallax data-speed=".2"
                                                 style="background-image: url(/images/hero/coworking2.jpg); height: 320px;"></div>
                                        </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- custom-jarallax end -->

            <!-- swiper -->
            <div class="row" id="swiper">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-0">Swiper</h5>
                            <p class="sub-header">
                                Using <a href="https://swiperjs.com/">Swipper</a> plugin, you can easily create
                                carousels. It's being used in almost all the pages where we are having slider in hero
                                element.
                            </p>

                            <h6 class="my-4">Simple Example</h6>

                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Swiper -->
                                    <div class="swiper demo-swiper">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <div class="swiper-slide-content">
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas1-{{ app()->getLocale() }}.png" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">Manage your saas business
                                                                with ease</h5>
                                                            <p class="text-muted">Make your saas application
                                                                stand out with high-quality landing page
                                                                designed and developed by
                                                                professional.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- swiper-slide End -->
                                            <div class="swiper-slide">
                                                <div class="swiper-slide-content">
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas2.jpg" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">The best way to showcase
                                                                your mobile app</h5>
                                                            <p class="text-muted">
                                                                Sed ut perspiciatis unde omnis iste natus error sit
                                                                voluptatem accusantium.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- swiper-slide End -->
                                            <div class="swiper-slide">
                                                <div class="swiper-slide-content">
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas3.jpg" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">Smart Solution that convert
                                                                Lead to Customer</h5>
                                                            <p class="text-muted">
                                                                Sed ut perspiciatis unde omnis iste natus error sit
                                                                voluptatem accusantium.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- swiper-slide End -->
                                        </div>
                                        <!-- swiper-wrapper End -->
                                        <div class="swiper-pagination"></div>
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>

                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#swiper-ex-1">
                                            Copy
                                        </button>
                                        <pre class="prettyprint lang-html escape" id="swiper-ex-1">
                                                    <div class="slider"><div class="swiper demo-swiper"><div
                                                                class="swiper-wrapper"><div class="swiper-slide"><div
                                                                        class="swiper-slide-content"><div
                                                                            class="row text-center"><div
                                                                                class="col"> <img
                                                                                    src="/images/hero/saas1-{{ app()->getLocale() }}.png"
                                                                                    alt="" class="w-75"/></div></div><div
                                                                            class="row text-center my-4 pb-5"><div
                                                                                class="col"><h5 class="fw-medium fs-16">Manage your saas business with ease</h5><p
                                                                                    class="text-muted">Make your saas application stand out with high-quality landing page designed and developed by professionals.</p></div></div></div></div><div
                                                                    class="swiper-slide"><div
                                                                        class="swiper-slide-content"><div
                                                                            class="row text-center"><div
                                                                                class="col"> <img
                                                                                    src="/images/hero/saas2.jpg"
                                                                                    alt="" class="w-75"/></div></div><div
                                                                            class="row text-center my-4 pb-5"><div
                                                                                class="col"><h5 class="fw-medium fs-16">The best way to showcase your mobile app</h5><p
                                                                                    class="text-muted"> Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p></div></div></div></div><div
                                                                    class="swiper-slide"><div
                                                                        class="swiper-slide-content"><div
                                                                            class="row text-center"><div
                                                                                class="col"> <img
                                                                                    src="/images/hero/saas3.jpg"
                                                                                    alt="" class="w-75"/></div></div><div
                                                                            class="row text-center my-4 pb-5"><div
                                                                                class="col"><h5 class="fw-medium fs-16">Smart Solution that convert Lead to Customer</h5><p
                                                                                    class="text-muted"> Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p></div></div></div></div></div><div
                                                                class="swiper-pagination"></div></div></div>
                                                </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- swiper end -->
        </div>
    </section>

@endsection

