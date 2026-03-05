@extends('layouts.base', ['title' => 'Prompt | UI Kit'])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true,'fixedWidth' => true, 'sticky' => true,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto','ctaButtonClass' => 'btn-outline-primary btn-sm' ])

    <section class="py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Avatars Start -->
                    <div class="row" id="avatars">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Avatars</h5>
                                    <p class="sub-header">
                                        Create and group avatars of different sizes and shapes with the size modifier
                                        css classes e.g.
                                        <code>avatar-{xl|lg|md|sm|xs}</code>. Using Bootstrap's naming convention, you
                                        can control size of
                                        avatar including standard avatar, or scale it up to different sizes.
                                    </p>

                                    <div class="py-4">
                                        <!-- Avatar Extra Large -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-xl rounded-sm shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Large -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-lg rounded-sm shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Medium -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-md rounded-sm shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Small -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-sm rounded-sm shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Extra Small -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-xs rounded-sm shadow-sm ms-5 mb-2 mb-xl-0">
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#avatar-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="avatar-ex-1">
                                            <img src="/images/avatars/img-7.jpg" alt="image"
                                                 class="img-fluid avatar-md rounded"/>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        Using an additional class <code>.rounded-circle</code>, you can create the
                                        rounded avatar.
                                    </p>

                                    <div class="py-4">
                                        <!-- Avatar Extra Large -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-xl rounded-circle shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Large -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-lg rounded-circle shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Medium -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-md rounded-circle shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Small -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-sm rounded-circle shadow-sm ms-5 mb-2 mb-xl-0">

                                        <!-- Avatar Extra Small -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-xs rounded-circle shadow-sm ms-5 mb-2 mb-xl-0">
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#avatar-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="avatar-ex-2">
                                            <img src="/images/avatars/img-7.jpg" alt="image"
                                                 class="img-fluid avatar-md rounded-circle"/>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        Using an additional class <code>.avatar-border</code>, you can give a nice
                                        border.
                                    </p>

                                    <div class="py-3">
                                        <!-- Avatar Large -->
                                        <img src="/images/avatars/img-7.jpg" alt="image"
                                             class="img-fluid avatar-lg rounded-circle avatar-border">
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#avatar-ex-3">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="avatar-ex-3">
                                            <img src="/images/avatars/img-7.jpg" alt="image"
                                                 class="img-fluid avatar-lg rounded-circle avatar-border">
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        Wrap the list of avatars with class <code>.avatar-group</code> to group and show
                                        multiple avatars.
                                    </p>
                                    <div class="avatar-group">
                                        <a href="" class="avatar-group-item">
                                            <img src="/images/avatars/img-7.jpg" alt="image"
                                                 class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                        </a>
                                        <a href="" class="avatar-group-item">
                                            <img src="/images/avatars/img-2.jpg" alt="image"
                                                 class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                        </a>
                                        <a href="" class="avatar-group-item">
                                            <img src="/images/avatars/img-4.jpg" alt="image"
                                                 class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                        </a>
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#avatar-ex-4">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="avatar-ex-4">
                                            <div class="avatar-group"> <a href="" class="avatar-group-item"> <img
                                                        src="/images/avatars/img-7.jpg" alt="image"
                                                        class="img-fluid avatar-xs rounded-circle avatar-border"/> </a> <a
                                                    href="" class="avatar-group-item"> <img
                                                        src="/images/avatars/img-2.jpg" alt="image"
                                                        class="img-fluid avatar-xs rounded-circle avatar-border"/> </a> <a
                                                    href="" class="avatar-group-item"> <img
                                                        src="/images/avatars/img-4.jpg" alt="image"
                                                        class="img-fluid avatar-xs rounded-circle avatar-border"/> </a> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Avatars End -->

                    <!-- Blog Post Cards Start -->
                    <div class="row" id="blog-cards">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Blog Items</h5>
                                    <p class="sub-header">
                                        Using bootstrap's <code>.card</code>, you can create a card holding blog post.
                                    </p>

                                    <div class="row">
                                        <div class="col-lg-6 col-xl-4">
                                            <div class="card card-listing-item">
                                                <div class="card-img-top-overlay">
                                                    <div class="overlay"></div>
                                                    <span
                                                        class="card-badge top-right bg-danger text-white">Travel</span>

                                                    <div class="position-relative">
                                                        <img src="/images/photos/2.jpg" alt="" class="card-img-top"/>

                                                        <div class="shape text-white bottom">
                                                            @svg('/shapes/bottom')
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="">
                                                        <h4><a href="#" class="card-title-link">Top 10 must visit best
                                                                beaches of Goa</a></h4>
                                                        <p class="text-muted mb-2">
                                                            Goa and its beaches do not need an introduction! The state
                                                            is well known for its
                                                            spectacular beaches and it is very difficult...<a href="">read
                                                                more
                                                        </p>
                                                    </div>
                                                    <div class="pt-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <p class="mb-0">
                                                                    <i data-feather="user"
                                                                       class="icon icon-dual icon-xs"></i>
                                                                    <a href="" class="fs-14 align-middle">Emily
                                                                        Blunt</a>
                                                                </p>
                                                            </div>
                                                            <div class="col text-end">
                                                                <p class="mb-0">
                                                                    <i data-feather="calendar"
                                                                       class="icon icon-dual icon-xs"></i>
                                                                    <span
                                                                        class="fs-14 align-middle">11 March, 2020</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-xl-4 offset-xl-2">
                                            <div class="card card-listing-item">
                                                <div class="card-img-top-overlay">
                                                    <div class="overlay"></div>
                                                    <span
                                                        class="card-badge top-right bg-primary text-white">Travel</span>
                                                    <img src="/images/photos/2.jpg" alt="" class="card-img-top"/>

                                                    <div class="card-overlay-bottom">
                                                        <a href="" class="shadow-lg">
                                                            <img src="/images/avatars/img-7.jpg" alt="image"
                                                                 class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                                            <h6 class="d-inline text-white">Emily Blunt</h6>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="">
                                                        <h4><a href="#" class="card-title-link">Top 10 must visit best
                                                                beaches of Goa</a></h4>
                                                        <p class="text-muted mb-2">
                                                            Goa and its beaches do not need an introduction! The state
                                                            is well known for its
                                                            spectacular beaches and it is very difficult...<a href="">read
                                                                more</a>
                                                        </p>
                                                    </div>
                                                    <div class="pt-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <p class="mb-0">
                                                                    <i data-feather="calendar"
                                                                       class="icon icon-dual icon-xs"></i>
                                                                    <span
                                                                        class="fs-14 align-middle">11 March, 2020</span>
                                                                </p>
                                                            </div>
                                                            <div class="col text-end">
                                                                <p class="mb-0">
                                                                    <i data-feather="tag"
                                                                       class="icon icon-dual icon-xs align-bottom"></i>
                                                                    <span class="fs-14">#travel-diary</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="code-block mt-2">
                                                <h6>Example</h6>
                                                <button class="btn btn-sm btn-copy-clipboard"
                                                        data-clipboard-target="#blog-card-ex-1">Copy
                                                </button>

                                                <pre class="prettyprint lang-html escape" id="blog-card-ex-1">
                                                    <div class="card card-listing-item"> <div
                                                            class="card-img-top-overlay"> <div
                                                                class="overlay"></div> <span
                                                                class="card-badge top-right bg-success text-white">Travel</span> <div
                                                                class="position-relative"> <img
                                                                    src="/images/photos/2.jpg" alt=""
                                                                    class="card-img-top"/> <div
                                                                    class="shape text-white bottom"> @svg("/shapes/bottom")

                                                                </div> </div> </div> <div class="card-body"> <div
                                                                class=""> <h4 class=""><a href="#"
                                                                                          class="card-title-link">Top 10 must visit best beaches of Goa</a></h4> <p
                                                                    class="text-muted mb-2"> Goa and its beaches do not need an introduction! The state is well known for its spectacular beaches and it is very difficult...<a
                                                                        href="">read more </p> </div> <div class="pt-3"> <div
                                                                    class="row align-items-center"> <div
                                                                        class="col-auto"> <p class="mb-0"> <i
                                                                                data-feather="user"
                                                                                class="icon icon-dual icon-xs"></i> <a
                                                                                href="" class="fs-14 align-middle">Emily Blunt</a> </p> </div> <div
                                                                        class="col text-end"> <p class="mb-0"> <i
                                                                                data-feather="calendar"
                                                                                class="icon icon-dual icon-xs"></i> <span
                                                                                class="fs-14 align-middle">11 March, 2020</span> </p> </div> </div> </div> </div> </div>
                                                </pre>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="code-block mt-2">
                                                <h6>Example</h6>
                                                <button class="btn btn-sm btn-copy-clipboard"
                                                        data-clipboard-target="#blog-card-ex-2">Copy
                                                </button>

                                                <pre class="prettyprint lang-html escape" id="blog-card-ex-2">
                                                    <div class="card card-listing-item"> <div
                                                            class="card-img-top-overlay"> <div
                                                                class="overlay"></div> <span
                                                                class="card-badge top-right bg-success text-white">Travel</span> <img
                                                                src="/images/photos/2.jpg" alt="" class="card-img-top"/> <div
                                                                class="card-overlay-bottom"> <a href=""
                                                                                                class="shadow-lg"> <img
                                                                        src="/images/avatars/img-7.jpg" alt="image"
                                                                        class="img-fluid avatar-xs rounded-circle avatar-border"/> <h6
                                                                        class="d-inline text-white">Emily Blunt</h6> </a> </div> </div> <div
                                                            class="card-body"> <div class=""> <h4 class=""><a href="#"
                                                                                                              class="card-title-link">Top 10 must visit best beaches of Goa</a></h4> <p
                                                                    class="text-muted mb-2"> Goa and its beaches do not need an introduction! The state is well known for its spectacular beaches and it is very difficult...<a
                                                                        href="">read more</a> </p> </div> <div
                                                                class="pt-3"> <div class="row align-items-center"> <div
                                                                        class="col-auto"> <p class="mb-0"> <i
                                                                                data-feather="calendar"
                                                                                class="icon icon-dual icon-xs"></i> <span
                                                                                class="fs-14 align-middle">11 March, 2020</span> </p> </div> <div
                                                                        class="col text-end"> <p class="mb-0"> <i
                                                                                data-feather="tag"
                                                                                class="icon icon-dual icon-xs align-bottom"></i> <span
                                                                                class="fs-14">#travel-diary</span> </p> </div> </div> </div> </div> </div>
                                                </pre>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="sub-header mt-4">An example showing minimal details</p>
                                    <div class="row">
                                        <div class="col-lg-8 col-xl-5">
                                            <div class="card card-listing-item">
                                                <div class="card-img-top-overlay">
                                                    <div class="overlay-dark"></div>
                                                    <span
                                                        class="card-badge top-right bg-success text-white">Travel</span>
                                                    <img src="/images/photos/4.jpg" alt="" class="card-img-top"/>

                                                    <div class="card-overlay-bottom">
                                                        <h2><a href="#" class="text-white">Top 10 must visit best
                                                                beaches of Goa</a></h2>

                                                        <div class="avatar-group">
                                                            <a href="" class="avatar-group-item shadow-lg">
                                                                <img src="/images/avatars/img-7.jpg" alt="image"
                                                                     class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                                            </a>
                                                            <a href="" class="avatar-group-item shadow-lg">
                                                                <img src="/images/avatars/img-2.jpg" alt="image"
                                                                     class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                                            </a>
                                                            <a href="" class="avatar-group-item shadow-lg">
                                                                <img src="/images/avatars/img-4.jpg" alt="image"
                                                                     class="img-fluid avatar-xs rounded-circle avatar-border"/>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#blog-card-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="blog-card-ex-2">
                                            <div class="card card-listing-item"> <div class="card-img-top-overlay"> <div
                                                        class="overlay-dark"></div> <span
                                                        class="card-badge top-right bg-success text-white">Travel</span> <img
                                                        src="/images/photos/4.jpg" alt="" class="card-img-top"/> <div
                                                        class="card-overlay-bottom"> <h2> <a href="#"
                                                                                             class="text-white">Top 10 must visit best beaches of Goa</a> </h2> <div
                                                            class="avatar-group"> <a href=""
                                                                                     class="avatar-group-item shadow-lg"> <img
                                                                    src="/images/avatars/img-7.jpg" alt="image"
                                                                    class="img-fluid avatar-xs rounded-circle avatar-border"/> </a> <a
                                                                href="" class="avatar-group-item shadow-lg"> <img
                                                                    src="/images/avatars/img-2.jpg" alt="image"
                                                                    class="img-fluid avatar-xs rounded-circle avatar-border"/> </a> <a
                                                                href="" class="avatar-group-item shadow-lg"> <img
                                                                    src="/images/avatars/img-4.jpg" alt="image"
                                                                    class="img-fluid avatar-xs rounded-circle avatar-border"/> </a> </div> </div> </div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Blog Post Cards End -->

                    <!-- pricing-cards start -->
                    <div class="row" id="pricing-cards">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Pricing cards</h5>
                                    <p class="sub-header">
                                        Using bootstrap's <code>.card</code>, you can create a pricing card.
                                    </p>

                                    <div class="row mt-5">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="card border hoverable">
                                                <div class="card-body text-center">
                                                    <h4 class="my-0 text-primary">Starter</h4>
                                                    <h1 class="mb-0">
                                                        <span class="fw-normal text-muted fs-13 align-top">$</span>
                                                        <span class="fw-bolder display-5">49</span>
                                                        <span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span>
                                                    </h1>

                                                    <ul class="list-unstyled border-top py-4 mt-4 text-start">
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Up to 600 minutes usage time</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Use for personal only</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Add up to 10 attendees</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Technical support via email</span>
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-soft-primary d-block">Purchase Now</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-xl-4">
                                            <div class="card border hoverable">
                                                <div class="card-body text-center">
                                                    <h4 class="my-0 text-primary">Professional</h4>
                                                    <h1 class="mb-0">
                                                        <span class="fw-normal text-muted fs-13 align-top">$</span>
                                                        <span class="fw-bolder display-5">99</span>
                                                        <span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span>
                                                    </h1>

                                                    <ul class="list-unstyled border-top py-4 mt-4 text-start">
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Up to 6000 minutes usage time</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Use for personal or a commercial client</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Add up to 100 attendees</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Up to 5 teams</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Technical support via email</span>
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-primary d-block">Purchase Now</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-xl-4">
                                            <div class="card border hoverable">
                                                <div class="card-body text-center">
                                                    <h4 class="my-0 text-primary">Enterprise</h4>
                                                    <h1 class="mb-0">
                                                        <span class="fw-normal text-muted fs-13 align-top">$</span>
                                                        <span class="fw-bolder display-5">599</span>
                                                        <span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span>
                                                    </h1>

                                                    <ul class="list-unstyled border-top py-4 mt-4 text-start">
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Unlimited usage time</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Use for personal or a commercial client</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Add Unlimited attendees</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>24x7 Technical support via phone</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Technical support via email</span>
                                                        </li>
                                                    </ul>
                                                    <a href="#" class="btn btn-soft-primary d-block">Purchase Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block mt-3">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#pricing-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="pricing-ex-1">
                                            <div class="card border"> <div class="card-body text-center"> <h4
                                                        class="my-0 text-primary">Starter</h4> <h1 class="mb-0"><span
                                                            class="fw-normal text-muted fs-13 align-top">$</span><span
                                                            class="fw-bolder display-5">49</span><span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span></h1> <ul
                                                        class="list-unstyled border-top py-4 mt-4 text-start"> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Up to 600 minutes usage time</span> </li> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Use for personal only</span> </li> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Add up to 10 attendees</span> </li> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Technical support via email</span> </li> </ul> <a
                                                        href="#" class="btn btn-soft-primary d-block">Purchase Now</a> </div> </div>
                                        </pre>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-lg-4 col-xl-4">
                                            <div class="card border hoverable">
                                                <div class="card-body">
                                                    <h4 class="my-0 text-primary">Starter</h4>
                                                    <h1>
                                                        <span class="fw-normal text-muted fs-13 align-top">$</span>
                                                        <span class="fw-bolder display-5">49</span>
                                                        <span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span>
                                                    </h1>
                                                    <a href="#" class="btn btn-soft-success d-block mt-3">Purchase
                                                        Now</a>

                                                    <ul class="list-unstyled border-top pt-4 mt-4 text-start">
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Up to 600 minutes usage time</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Use for personal only</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Add up to 10 attendees</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Technical support via email</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-xl-4">
                                            <div class="card border hoverable">
                                                <div class="card-img-top-overlay d-none d-lg-block">
                                                    <span class="card-badge top-right bg-warning text-white shadow-sm">
                                                        Recommended
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <h4 class="my-0 text-primary">Professional</h4>
                                                    <h1>
                                                        <span class="fw-normal text-muted fs-13 align-top">$</span>
                                                        <span class="fw-bolder display-5">99</span>
                                                        <span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span>
                                                    </h1>
                                                    <a href="#" class="btn btn-primary d-block mt-3">Purchase Now</a>

                                                    <ul class="list-unstyled border-top pt-4 mt-4 text-start">
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Up to 6000 minutes usage time</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Use for personal or a commercial client</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Add up to 100 attendees</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Up to 5 teams</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Technical support via email</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-xl-4">
                                            <div class="card border hoverable">
                                                <div class="card-body">
                                                    <h4 class="my-0 text-primary">Enterprise</h4>
                                                    <h1>
                                                        <span class="fw-normal text-muted fs-13 align-top">$</span>
                                                        <span class="fw-bolder display-5">599</span>
                                                        <span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span>
                                                    </h1>
                                                    <a href="#" class="btn btn-soft-success d-block mt-3">Purchase
                                                        Now</a>

                                                    <ul class="list-unstyled border-top pt-4 mt-4 text-start">
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Unlimited usage time</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Use for personal or a commercial client</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Add Unlimited attendees</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>24x7 Technical support via phone</span>
                                                        </li>
                                                        <li class="py-2 d-flex align-items-center">
                                                            <i class="icon icon-xs text-success align-middle me-2"
                                                               data-feather="check"></i>
                                                            <span>Technical support via email</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="code-block mt-3">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#pricing-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="pricing-ex-2">
                                            <div class="card border hoverable"> <div class="card-body"> <h4
                                                        class="my-0 text-primary">Starter</h4> <h1 class=""><span
                                                            class="fw-normal text-muted fs-13 align-top">$</span><span
                                                            class="fw-bolder display-5">49</span><span
                                                            class="fw-normal text-muted fs-13 align-middle"> / month</span></h1> <a
                                                        href="#"
                                                        class="btn btn-soft-success d-block mt-3">Purchase Now</a> <ul
                                                        class="list-unstyled border-top pt-4 mt-4 text-start"> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Up to 600 minutes usage time</span> </li> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Use for personal only</span> </li> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Add up to 10 attendees</span> </li> <li
                                                            class="py-1 d-flex align-items-center"> <i
                                                                class="icon icon-xs text-success align-middle me-2"
                                                                data-feather="check"></i> <span>Technical support via email</span> </li> </ul> </div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- pricing-cards end -->

                    <!-- gallery start -->
                    <div class="row" id="gallery">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Gallery</h5>
                                    <p class="sub-header">
                                        Using <a href="https://github.com/dimsemenov/Magnific-Popup/">Magnific Popup</a>
                                        plugin, you can
                                        easily create a gallery of images, videos or other custom items.
                                        It's a fast, light and responsive lightbox plugin, for jQuery and Zepto.js.
                                    </p>

                                    <h6 class="mt-4 mb-1">Simple Example</h6>
                                    <p class="sub-header mb-4">Just specify data attribute <code>data-toggle="image-gallery"</code>
                                        to any
                                        div element containing your gallery items to enabled it.</p>

                                    <div data-toggle="image-gallery" data-delegate="a" data-type="image"
                                         data-enable-gallery="true">
                                        <div class="row">
                                            <div class="col-lg-4 col-6">
                                                <a href="/images/photos/3.jpg" data-title="A lavish inside style">
                                                    <div class="card">
                                                        <img src="/images/photos/3.jpg" alt=""
                                                             class="img-fluid rounded-sm shadow">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <a href="/images/photos/4.jpg" data-title="Another inside view">
                                                    <div class="card">
                                                        <img src="/images/photos/4.jpg" alt=""
                                                             class="img-fluid rounded-sm shadow">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <a href="/images/photos/1.jpg"
                                                   data-title="Spacious sitting arrangement">
                                                    <div class="card">
                                                        <img src="/images/photos/1.jpg" alt=""
                                                             class="img-fluid rounded-sm shadow">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <a href="/images/photos/2.jpg" data-title="A lavish outside view">
                                                    <div class="card">
                                                        <img src="/images/photos/2.jpg" alt=""
                                                             class="img-fluid rounded-sm shadow">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <a href="/images/photos/10.jpg" data-title="Kitchen">
                                                    <div class="card">
                                                        <img src="/images/photos/10.jpg" alt=""
                                                             class="img-fluid rounded-sm shadow">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-lg-4 col-6">
                                                <a href="/images/photos/5.jpg" data-title="Lavish styled bedroom">
                                                    <div class="card">
                                                        <img src="/images/photos/5.jpg" alt=""
                                                             class="img-fluid rounded-sm shadow">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#gallery-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="gallery-ex-1">
                                            <div data-toggle="image-gallery" data-delegate="a" data-type="image"
                                                 data-enable-gallery="true"> <div class="row"> <div
                                                        class="col-lg-4 col-6"> <a href="/images/photos/3.jpg"
                                                                                   data-title="A lavish inside style"> <div
                                                                class="card"> <img src="/images/photos/3.jpg" alt=""
                                                                                   class="img-fluid rounded-sm shadow"> </div> </a> </div> <div
                                                        class="col-lg-4 col-6"> <a href="/images/photos/4.jpg"
                                                                                   data-title="Another inside view"> <div
                                                                class="card"> <img src="/images/photos/4.jpg" alt=""
                                                                                   class="img-fluid rounded-sm shadow"> </div> </a> </div> <div
                                                        class="col-lg-4 col-6"> <a href="/images/photos/1.jpg"
                                                                                   data-title="Spacious sitting arrangement"> <div
                                                                class="card"> <img src="/images/photos/1.jpg" alt=""
                                                                                   class="img-fluid rounded-sm shadow"> </div> </a> </div> <div
                                                        class="col-lg-4 col-6"> <a href="/images/photos/2.jpg"
                                                                                   data-title="A lavish outside view"> <div
                                                                class="card"> <img src="/images/photos/2.jpg" alt=""
                                                                                   class="img-fluid rounded-sm shadow"> </div> </a> </div> <div
                                                        class="col-lg-4 col-6"> <a href="/images/photos/10.jpg"
                                                                                   data-title="Kitchen"> <div
                                                                class="card"> <img src="/images/photos/10.jpg" alt=""
                                                                                   class="img-fluid rounded-sm shadow"> </div> </a> </div> <div
                                                        class="col-lg-4 col-6"> <a href="/images/photos/5.jpg"
                                                                                   data-title="Lavish styled bedroom"> <div
                                                                class="card"> <img src="/images/photos/5.jpg" alt=""
                                                                                   class="img-fluid rounded-sm shadow"> </div> </a> </div> </div> </div>
                                        </pre>
                                    </div>

                                    <div data-toggle="image-gallery" data-delegate="a" data-type="image"
                                         data-enable-gallery="true">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="card border-0 shadow-none overflow-hidden rounded">
                                                    <a href="/images/photos/9.jpg">
                                                        <img src="/images/photos/11.jpg" alt=""
                                                             class="img-fluid mx-auto d-block rounded-sm">
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- Col End -->
                                            <div class="col-lg-7">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div
                                                            class="gallery-box card border-0 shadow overflow-hidden rounded-sm">
                                                            <a href="/images/photos/5.jpg">
                                                                <img src="/images/photos/5.jpg" alt=""
                                                                     class="img-fluid mx-auto d-block">
                                                            </a>
                                                        </div>
                                                        <div
                                                            class="gallery-box card border-0 shadow overflow-hidden rounded-sm">
                                                            <a href="/images/photos/7.jpg">
                                                                <img src="/images/photos/7.jpg" alt=""
                                                                     class="img-fluid mx-auto d-block">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <!-- Col End -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="gallery-box card border-0 shadow overflow-hidden rounded-sm">
                                                            <a href="/images/photos/8.jpg">
                                                                <img src="/images/photos/8.jpg" alt=""
                                                                     class="img-fluid mx-auto d-block">
                                                            </a>
                                                        </div>

                                                        <div
                                                            class="gallery-box card border-0 shadow overflow-hidden rounded-sm">
                                                            <a href="/images/photos/10.jpg">
                                                                <img src="/images/photos/10.jpg" alt=""
                                                                     class="img-fluid mx-auto d-block">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <!-- Col End -->
                                                </div>
                                                <!-- Row End -->
                                            </div>
                                            <!-- Col End -->
                                        </div>
                                    </div>

                                    <div class="code-block">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#gallery-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="gallery-ex-2">
                                            <div data-toggle="image-gallery" data-delegate="a" data-type="image"
                                                 data-enable-gallery="true"> <div class="row"> <div class="col-lg-5"> <div
                                                            class="card border-0 shadow overflow-hidden rounded"> <a
                                                                href="/images/photos/6.jpg"> <img
                                                                    src="/images/photos/6.jpg" alt=""
                                                                    class="img-fluid mx-auto d-block"> </a> </div> </div>
                                                    <!-- Col End --> <div class="col-lg-7"> <div class="row"> <div
                                                                class="col-md-6"> <div
                                                                    class="gallery-box card border-0 shadow overflow-hidden rounded-sm"> <a
                                                                        href="/images/photos/5.jpg"> <img
                                                                            src="/images/photos/5.jpg" alt=""
                                                                            class="img-fluid mx-auto d-block"> </a> </div> <div
                                                                    class="gallery-box card border-0 shadow overflow-hidden rounded-sm"> <a
                                                                        href="/images/photos/7.jpg"> <img
                                                                            src="/images/photos/7.jpg" alt=""
                                                                            class="img-fluid mx-auto d-block"> </a> </div> </div>
                                                            <!-- Col End --> <div class="col-md-6"> <div
                                                                    class="gallery-box card border-0 shadow overflow-hidden rounded-sm"> <a
                                                                        href="/images/photos/8.jpg"> <img
                                                                            src="/images/photos/8.jpg" alt=""
                                                                            class="img-fluid mx-auto d-block"> </a> </div> <div
                                                                    class="gallery-box card border-0 shadow overflow-hidden rounded-sm"> <a
                                                                        href="/images/photos/10.jpg"> <img
                                                                            src="/images/photos/10.jpg" alt=""
                                                                            class="img-fluid mx-auto d-block"> </a> </div> </div>
                                                            <!-- Col End --> </div> <!-- Row End --> </div>
                                                    <!-- Col End --> </div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- gallery end -->

                    <!-- icons -->
                    <div class="row" id="icons">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Icons</h5>
                                    <p class="sub-header">
                                        Prompt comes with two icon libraries:
                                        <a href="https://feathericons.com/">Feather Icons</a>
                                        and premium svg based two tones icons.
                                    </p>

                                    <div class="mt-3 row">
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/code/Compiling")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/code/Git#4")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/code/Github")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/code/Plus")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/code/Settings#4")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/code/Terminal")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/communication/Address-card")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/communication/Call#1")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/communication/Chat#1")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/communication/Clipboard-list")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/communication/Group-chat")
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="icon icon-sm">
                                                @svg("/duotone-icons/communication/Mail-heart")
                                            </span>
                                        </div>
                                    </div>

                                    <p class="pt-4">
                                        You can check out all the available icons in folder
                                        <code class="fw-semibold">resources/svg/icons/duotone-icons</code> folder.
                                    </p>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#icon-ex-21">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="icon-ex-21">
                                            <span class="icon">&commat;&commat;include("/images/icons/duotone-icons/communication/Mail-heart")</span>
                                        </pre>
                                    </div>

                                    <!-- colors -->
                                    <div class="mt-5">
                                        <h5 class="mb-1">Colors</h5>
                                        <p class="">
                                            Use text modifier class
                                            <code>.text-*</code> to style the icon.
                                            E.g. <code>text-{primary|secondary|success|danger|info|warning}.</code>
                                        </p>

                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="icon icon-sm text-primary">
                                                    @svg("/duotone-icons/communication/Mail-heart")
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-sm text-secondary">
                                                    @svg("/duotone-icons/communication/Call#1")
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-sm text-success">
                                                    @svg("/duotone-icons/communication/Chat#1")
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-sm text-danger">
                                                    @svg("/duotone-icons/communication/Clipboard-list")
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-sm text-info">
                                                    @svg("/duotone-icons/communication/Group-chat")
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#icon-ex-22">Copy
                                        </button>
                                        <pre class="prettyprint lang-html escape" id="icon-ex-22">
                                            <span class="icon text-primary">&commat;svg("/duotone-icons/communication/Mail-heart")</span>
                                        </pre>
                                    </div>
                                    <!-- colors end -->

                                    <!-- sizes start -->
                                    <div class="mt-5">
                                        <h5 class="mb-1">Sizes</h5>
                                        <p class="">
                                            Use size modifier class
                                            <code>.icon-{xxs|xs|sm|md|lg|xl|xxl}</code> to change the size.
                                        </p>

                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="icon icon-xxl text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-xl text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-lg text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-md text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-sm text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-xs text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="icon icon-xxs text-primary">
                                                    @svg('/duotone-icons/general/Half-heart')
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#icon-ex-23">Copy
                                        </button>
                                        <pre class="prettyprint lang-html escape" id="icon-ex-23">
                                            <span class="icon text-primary">&commat;svg("duotone-icons/communication/Mail-heart")</span>
                                        </pre>
                                    </div>
                                    <!-- sizes end -->


                                    <div class="my-5"></div>
                                    <h5>Feather Icons</h5>
                                    <p class="sub-header">
                                        Feather is a collection of simply beautiful svg based open source icons. Each
                                        icon is designed with an emphasis on simplicity, consistency, and flexibility.
                                        To use an icon on your page, add a <code>data-feather</code> attribute with the
                                        icon name to an element.
                                    </p>

                                    <div class="row pt-3">
                                        <div class="col-md-auto">
                                            <i data-feather="activity" class="me-2"></i>
                                            <i data-feather="shopping-bag" class="me-2"></i>
                                            <i data-feather="credit-card" class="me-2"></i>
                                            <i data-feather="message-square" class="me-2"></i>
                                            <i data-feather="map-pin" class="me-2"></i>
                                            <i data-feather="bell" class="me-2"></i>
                                            <i data-feather="calendar" class="me-2"></i>
                                            <i data-feather="map"></i>
                                        </div>
                                    </div>
                                    <p class="sub-header mt-4">
                                        For a complete list of icons, check
                                        <a href="https://feathericons.com/" class="text-primary">here</a>.
                                    </p>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#icon-ex-1">Copy
                                        </button>
                                        <pre class="prettyprint lang-html escape" id="icon-ex-1">
                                            <i data-feather="activity"></i>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        Use modifier class <code>.icon-dual</code> to convert it into two-tone. All the
                                        color variations are available as well. E.g. <code>icon-dual-{primary|secondary|success|danger|info|warning}.</code>
                                    </p>

                                    <div class="row">
                                        <div class="col-auto">
                                            <i data-feather="activity" class="icon-dual me-2"></i>
                                            <i data-feather="shopping-bag" class="icon-dual-primary me-2"></i>
                                            <i data-feather="credit-card" class="icon-dual-secondary me-2"></i>
                                            <i data-feather="message-square" class="icon-dual-success me-2"></i>
                                            <i data-feather="map-pin" class="icon-dual-danger me-2"></i>
                                            <i data-feather="bell" class="icon-dual-info me-2"></i>
                                            <i data-feather="calendar" class="icon-dual-warning"></i>
                                        </div>
                                    </div>

                                    <p class="sub-header mt-4">
                                        Use size modifier class <code>.icon-{xxs|xs|sm|md|lg|xl|xxl}</code> to change
                                        the size.</code>
                                    </p>

                                    <div class="row">
                                        <div class="col-auto">
                                            <i data-feather="message-circle" class="icon-xxl me-2"></i>
                                            <i data-feather="message-circle" class="icon-xl me-2"></i>
                                            <i data-feather="message-circle" class="icon-lg me-2"></i>
                                            <i data-feather="message-circle" class="icon-md me-2"></i>
                                            <i data-feather="message-circle" class="icon-sm me-2"></i>
                                            <i data-feather="message-circle" class="icon-xs me-2"></i>
                                            <i data-feather="message-circle" class="icon-xxs me-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- icons end -->
                </div>
            </div>
        </div>
    </section>

@endsection
