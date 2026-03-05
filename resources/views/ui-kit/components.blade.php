@extends('layouts.base', ['title' => 'Prompt | UI Kit'])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true,'fixedWidth' => true, 'sticky' => true,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto','ctaButtonClass' => 'btn-outline-primary btn-sm' ])

    <section class="py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Alerts Start -->
                    <div class="row" id="alerts">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Alerts</h5>
                                    <p class="sub-header">
                                        Provide contextual feedback messages for typical user actions with the handful
                                        of available and flexible alert messages.
                                    </p>

                                    <div role="alert" class="alert text-primary bg-white border alert-dismissible">
                                        <div class="d-flex align-items-start">
                                            <span class="badge badge-soft-primary align-self-center me-3">Info</span>
                                            <div class="w-100">You should select the desired categories while creating
                                                listing
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>

                                    <div role="alert" class="alert text-success bg-white border alert-dismissible">
                                        <div class="d-flex align-items-start">
                                            <span class="badge badge-soft-success align-self-center me-3">Success</span>
                                            <div class="w-100">Your booking is confirmed by <a href=""
                                                                                               class="text-success alert-link">Mr.
                                                    Shreyan</a></div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>

                                    <div role="alert" class="alert text-danger bg-white border alert-dismissible">
                                        <div class="d-flex align-items-start">
                                            <span class="badge badge-soft-danger align-self-center me-3">Ohh no!</span>
                                            <div class="w-100">Please check the input you have specified</div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>

                                    <div role="alert" class="alert text-warning bg-white border alert-dismissible">
                                        <div class="d-flex align-items-start">
                                            <span
                                                class="badge badge-soft-warning align-self-center me-3">Warning!</span>
                                            <div class="w-100">The number of tickets you have selected might not get
                                                confirmed
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>

                                    <div role="alert" class="alert text-info bg-white border alert-dismissible">
                                        <div class="d-flex align-items-start">
                                            <span class="badge badge-soft-info align-self-center me-3">Info</span>
                                            <div class="w-100">You might want to book return flight to save 25% on
                                                overall booking amount
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#alert-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="alert-ex-1">
                                            <div role="alert"
                                                 class="alert text-danger bg-white border alert-dismissible"><div
                                                    class="d-flex align-items-start"><span
                                                        class="badge badge-soft-danger align-self-center me-3">Ohh no!</span><div
                                                        class="w-100">Please check the input you have specified</div></div><button
                                                    type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button></div>
                                        </pre>
                                    </div>

                                    <div role="alert" class="alert alert-primary alert-dismissible">
                                        This is a primary alert—check it out!
                                    </div>
                                    <div role="alert" class="alert alert-secondary alert-dismissible">
                                        This is a secondary alert—check it out!
                                    </div>
                                    <div role="alert" class="alert alert-success alert-dismissible">
                                        This is a success alert—check it out!
                                    </div>
                                    <div role="alert" class="alert alert-danger alert-dismissible">
                                        This is a danger alert—check it out!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>
                                    <div role="alert" class="alert alert-warning alert-dismissible">
                                        This is a warning alert—check it out!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>
                                    <div role="alert" class="alert alert-info alert-dismissible">
                                        This is a info alert—check it out!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>

                                    <div class="code-block mt-2">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#alert-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="alert-ex-2">
                                            <div role="alert" class="alert alert-primary alert-dismissible">This is a primary alert—check it out!<button
                                                    type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button></div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Alerts End -->

                    <!-- Accordions Start -->
                    <div class="row">
                        <div class="col">
                            <div class="card" id="accordions">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Accordions</h5>
                                    <p class="sub-header">
                                        Toggle the visibility of content across your project with a few classes and our
                                        JavaScript plugins.
                                    </p>

                                    <div class="accordion custom-accordionwitharrow mt-3" id="accordionExample">
                                        <div class="card mb-1 shadow-none border">
                                            <a href="" class="text-dark" data-bs-toggle="collapse"
                                               data-bs-target="#collapseOne" aria-expanded="true"
                                               aria-controls="collapseOne">
                                                <div class="card-header" id="headingOne">
                                                    <h5 class="my-1">What is Lorem Ipsum?
                                                        <i class="icon-xs accordion-arrow"
                                                           data-feather="chevron-down"></i>
                                                    </h5>
                                                </div>
                                            </a>
                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                                 data-bs-parent="#accordionExample">
                                                <div class="card-body text-muted pt-1">
                                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus
                                                    terry richardson ad squid. 3 wolf moon officia aute, non cupidatat
                                                    skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
                                                    Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid
                                                    single-origin coffee nulla assumenda shoreditch et. Nihil anim
                                                    keffiyeh helvetica, craft beer labore wes anderson cred nesciunt
                                                    sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings
                                                    occaecat craft beer farm-to-table, raw denim aesthetic synth
                                                    nesciunt you probably haven't heard of them accusamus labore
                                                    sustainable VHS.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card mb-1 shadow-none border">
                                            <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                               data-bs-target="#collapseTwo" aria-expanded="false"
                                               aria-controls="collapseTwo">
                                                <div class="card-header" id="headingTwo">
                                                    <h5 class="my-1">Why do we use it?
                                                        <i class="icon-xs accordion-arrow"
                                                           data-feather="chevron-down"></i>
                                                    </h5>
                                                </div>
                                            </a>
                                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                                 data-bs-parent="#accordionExample">
                                                <div class="card-body text-muted pt-1">
                                                    Anim pariatur cliche reprehenderit,
                                                    enim eiusmod high life accusamus terry richardson ad squid. 3 wolf
                                                    moon officia aute, non cupidatat skateboard dolor brunch. Food truck
                                                    quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                                    aliqua put a bird on it squid single-origin coffee nulla assumenda
                                                    shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes
                                                    anderson cred nesciunt sapiente ea proident. Ad vegan excepteur
                                                    butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw
                                                    denim aesthetic synth nesciunt you probably haven't heard of them
                                                    accusamus labore sustainable VHS.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card mb-0 shadow-none border">
                                            <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                               data-bs-target="#collapseThree" aria-expanded="false"
                                               aria-controls="collapseThree">
                                                <div class="card-header" id="headingThree">
                                                    <h5 class="my-1">Where does it come from?
                                                        <i class="icon-xs accordion-arrow"
                                                           data-feather="chevron-down"></i>
                                                    </h5>
                                                </div>
                                            </a>
                                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                                 data-bs-parent="#accordionExample">
                                                <div class="card-body text-muted pt-1">
                                                    Anim pariatur cliche reprehenderit,
                                                    enim eiusmod high life accusamus terry richardson ad squid. 3 wolf
                                                    moon officia aute, non cupidatat skateboard dolor brunch. Food truck
                                                    quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                                    aliqua put a bird on it squid single-origin coffee nulla assumenda
                                                    shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes
                                                    anderson cred nesciunt sapiente ea proident. Ad vegan excepteur
                                                    butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw
                                                    denim aesthetic synth nesciunt you probably haven't heard of them
                                                    accusamus labore sustainable VHS.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#accordion-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="accordion-ex-1">
                                            <div class="accordion custom-accordionwitharrow mt-3" id="accordionExample"> <div
                                                    class="card mb-1 shadow-none border"> <a href="" class="text-dark"
                                                                                             data-bs-toggle="collapse"
                                                                                             data-bs-target="#collapseOne"
                                                                                             aria-expanded="true"
                                                                                             aria-controls="collapseOne"> <div
                                                            class="card-header" id="headingOne"> <h5 class="my-1">What is Lorem Ipsum? <i
                                                                    class="icon-xs accordion-arrow"
                                                                    data-feather="chevron-down"></i> </h5> </div> </a> <div
                                                        id="collapseOne" class="collapse show"
                                                        aria-labelledby="headingOne" data-parent="#accordionExample"> <div
                                                            class="card-body text-muted pt-1"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. </div> </div> </div> <div
                                                    class="card mb-1 shadow-none border"> <a href=""
                                                                                             class="text-dark collapsed"
                                                                                             data-bs-toggle="collapse"
                                                                                             data-bs-target="#collapseTwo"
                                                                                             aria-expanded="false"
                                                                                             aria-controls="collapseTwo"> <div
                                                            class="card-header" id="headingTwo"> <h5 class="my-1">Why do we use it? <i
                                                                    class="icon-xs accordion-arrow"
                                                                    data-feather="chevron-down"></i> </h5> </div> </a> <div
                                                        id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                                        data-parent="#accordionExample"> <div
                                                            class="card-body text-muted pt-1">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</div> </div> </div> <div
                                                    class="card mb-0 shadow-none border"> <a href=""
                                                                                             class="text-dark collapsed"
                                                                                             data-bs-toggle="collapse"
                                                                                             data-bs-target="#collapseThree"
                                                                                             aria-expanded="false"
                                                                                             aria-controls="collapseThree"> <div
                                                            class="card-header" id="headingThree"> <h5 class="my-1">Where does it come from? <i
                                                                    class="icon-xs accordion-arrow"
                                                                    data-feather="chevron-down"></i> </h5> </div> </a> <div
                                                        id="collapseThree" class="collapse"
                                                        aria-labelledby="headingThree" data-parent="#accordionExample"> <div
                                                            class="card-body text-muted pt-1">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</div> </div> </div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Accordions End -->

                    <!-- Badges Start -->
                    <div class="row" id="badges">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Badges</h5>
                                    <p class="sub-header">
                                        Badges scale to match the size of the immediate parent element by
                                        using relative font sizing and <code>em</code> units.
                                    </p>

                                    <h1>Example heading <span class="badge bg-secondary">New</span></h1>
                                    <h2>Example heading <span class="badge bg-secondary">New</span></h2>
                                    <h3>Example heading <span class="badge bg-secondary">New</span></h3>
                                    <h4>Example heading <span class="badge bg-secondary">New</span></h4>
                                    <h5>Example heading <span class="badge bg-secondary">New</span></h5>
                                    <h6>Example heading <span class="badge bg-secondary">New</span></h6>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#badge-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="badge-ex-2">
                                            <h4>Example heading <span class="badge bg-secondary">New</span></h4>
                                        </pre>
                                    </div>

                                    <p class="mt-4">Badges can be used as part of links or buttons to provide a
                                        counter.</p>
                                    <button type="button" class="btn btn-primary">
                                        Notifications <span class="badge bg-light text-dark">4</span>
                                    </button>

                                    <p class="mt-4">
                                        Add any of the below mentioned modifier classes to change the appearance of a
                                        badge.
                                    </p>

                                    <span class="badge bg-primary">Primary</span>
                                    <span class="badge bg-secondary">Secondary</span>
                                    <span class="badge bg-success">Success</span>
                                    <span class="badge bg-danger">Danger</span>
                                    <span class="badge bg-warning">Warning</span>
                                    <span class="badge bg-info">Info</span>
                                    <span class="badge bg-orange">Orange</span>
                                    <span class="badge bg-light text-dark">Light</span>
                                    <span class="badge bg-dark">Dark</span>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#badge-ex-1">copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="badge-ex-1">
                                            <span class="badge bg-primary">Primary</span>
                                        </pre>
                                    </div>

                                    <p class="mt-4">
                                        Use the <code>.rounded-pill</code> modifier class to make badges more
                                        rounded (with a larger border-radius and additional horizontal padding).
                                    </p>
                                    <span class="badge bg-primary rounded-pill">Pill</span>
                                    <span class="badge bg-primary rounded-pill badge-md">Badge-md</span>
                                    <span class="badge bg-primary rounded-pill badge-lg">Badge-lg</span>

                                    <p class="mt-4">
                                        Use the <code>.badge-soft-*</code> modifier class to make badges soft</p>
                                    <span class="badge badge-soft-primary">Primary</span>
                                    <span class="badge badge-soft-secondary">Secondary</span>
                                    <span class="badge badge-soft-success">Success</span>
                                    <span class="badge badge-soft-danger">Danger</span>
                                    <span class="badge badge-soft-warning">Warning</span>
                                    <span class="badge badge-soft-info">Info</span>
                                    <span class="badge badge-soft-orange">Orange</span>
                                    <span class="badge badge-soft-dark">Dark</span>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#badge-ex-3">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="badge-ex-3">
                                            <span class="badge badge-soft-primary">Primary</span>
                                        </pre>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Badges End -->

                    <!-- Breadcrumb Start -->
                    <div class="row" id="breadcrumb">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Breadcrumb</h5>
                                    <p class="sub-header">
                                        Indicate the current page's location within a navigational hierarchy that
                                        automatically adds separators via CSS.
                                    </p>

                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item active" aria-current="page">Home</li>
                                        </ol>
                                    </nav>

                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Library</li>
                                        </ol>
                                    </nav>

                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a href="#"><i data-feather="home" class="icon-xs"></i></a>
                                            </li>
                                            <li class="breadcrumb-item"><a href="#">Library</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Data</li>
                                        </ol>
                                    </nav>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#bredcrumb-ex-3">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="bredcrumb-ex-3">
                                            <nav aria-label="breadcrumb"><ol class="breadcrumb"><li
                                                        class="breadcrumb-item"><a href="#"><i data-feather="home"
                                                                                               class="icon-xs"></i></a></li><li
                                                        class="breadcrumb-item"><a href="#">Library</a></li><li
                                                        class="breadcrumb-item active"
                                                        aria-current="page">Data</li></ol></nav>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <!-- Buttons Start -->
                    <div class="row" id="buttons">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <h5 class="card-title mb-0">Buttons</h5>
                                        <p class="sub-header">
                                            Use the button classes on an <code>&lt;a&gt;</code>,
                                            <code>&lt;button&gt;</code>, or <code>&lt;input&gt;</code> element.
                                        </p>

                                        <div class="button-list">
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-primary">Primary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-secondary">
                                                Secondary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-success">Success
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-danger">Danger
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-warning">Warning
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-info">Info</button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-orange">Orange
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-dark">Dark</button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-link">Link</button>
                                        </div>

                                        <div class="code-block mt-4">
                                            <h6>Example</h6>

                                            <button class="btn btn-sm btn-copy-clipboard"
                                                    data-clipboard-target="#buttons-ex-1">Copy
                                            </button>

                                            <pre class="prettyprint lang-html escape" id="buttons-ex-1">
                                                <button type="button" class="btn btn-primary">Primary</button>
                                            </pre>
                                        </div>


                                        <p class="sub-header pt-2">
                                            In need of a button, but not the hefty background colors they bring? Replace
                                            the
                                            default modifier classes with the <code>.btn-outline-*</code> ones to remove
                                            all
                                            background images and colors on any button.
                                        </p>

                                        <div class="button-list">
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-primary">
                                                Primary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-secondary">
                                                Secondary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-success">
                                                Success
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-danger">
                                                Danger
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-warning">
                                                Warning
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-info">Info
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-orange">
                                                Orange
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-dark">Dark
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-outline-link">Link
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-white">White</button>
                                        </div>

                                        <div class="code-block mt-4">
                                            <h6>Example</h6>
                                            <button class="btn btn-sm btn-copy-clipboard"
                                                    data-clipboard-target="#buttons-ex-2">Copy
                                            </button>

                                            <pre class="prettyprint lang-html escape" id="buttons-ex-2">
                                                <button type="button" class="btn btn-outline-primary">Primary</button>
                                            </pre>
                                        </div>

                                        <p class="sub-header pt-2">
                                            Replace the default modifier classes with the <code>.btn-soft-*</code>
                                            ones to have a softer background color on any button.
                                        </p>

                                        <div class="button-list">
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-primary">
                                                Primary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-secondary">
                                                Secondary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-success">
                                                Success
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-danger">Danger
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-warning">
                                                Warning
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-info">Info
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-orange">Orange
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-dark">Dark
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-soft-link">Link
                                            </button>
                                        </div>

                                        <div class="code-block mt-4">
                                            <h6>Example</h6>
                                            <button class="btn btn-sm btn-copy-clipboard"
                                                    data-clipboard-target="#buttons-ex-3">Copy
                                            </button>

                                            <pre class="prettyprint lang-html escape" id="buttons-ex-3">
                                                <button type="button" class="btn btn-soft-primary">Primary</button>
                                            </pre>
                                        </div>

                                        <p class="sub-header pt-2">
                                            Add a class <code>.btn-rounded</code> with the default modifier classes to
                                            have rounded edges.
                                        </p>

                                        <div class="button-list mt-2">
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-primary">
                                                Primary
                                            </button>
                                            <button type="button"
                                                    class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-secondary">Secondary
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-success">
                                                Success
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-danger">
                                                Danger
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-warning">
                                                Warning
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-info">
                                                Info
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-orange">
                                                Orange
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-dark">
                                                Dark
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-xl-0 btn btn-rounded btn-link">
                                                Link
                                            </button>
                                        </div>

                                        <div class="code-block mt-4">
                                            <h6>Example</h6>

                                            <button class="btn btn-sm btn-copy-clipboard"
                                                    data-clipboard-target="#buttons-ex-4">Copy
                                            </button>

                                            <pre class="prettyprint lang-html escape" id="buttons-ex-4">
                                                <button type="button"
                                                        class="btn btn-rounded btn-primary">Primary</button>
                                            </pre>
                                        </div>

                                        <p class="sub-header pt-2">
                                            Fancy larger or smaller buttons? Add <code>.btn-lg</code> or
                                            <code>.btn-sm</code> for additional sizes.
                                        </p>
                                        <div class="button-list">
                                            <button type="button" class="btn mb-2 mb-sm-0 btn-primary btn-lg me-2">
                                                Button Large
                                            </button>
                                            <button type="button" class="btn mb-2 mb-sm-0 btn-primary me-2">Button
                                                Regular
                                            </button>
                                            <button type="button" class="btn mb-2 mb-sm-0 btn-primary btn-sm">Button
                                                Small
                                            </button>
                                        </div>

                                        <div class="code-block mt-4">
                                            <h6>Example</h6>

                                            <button class="btn btn-sm btn-copy-clipboard"
                                                    data-clipboard-target="#buttons-ex-4">Copy
                                            </button>

                                            <pre class="prettyprint lang-html escape" id="buttons-ex-4">
                                                <button type="button"
                                                        class="btn btn-primary btn-lg">Button Large</button>
                                            </pre>
                                        </div>

                                        <p class="sub-header pt-2">Buttons with icon - variations</p>
                                        <div class="button-list">
                                            <button type="button" class="me-2 mb-2 mb-sm-0 btn btn-primary">
                                                <i class="icon-xs me-1" data-feather="play"></i> Button with icon on
                                                left
                                            </button>
                                            <button type="button" class="me-2 mb-2 mb-sm-0 btn btn-primary">
                                                Button with icon on right <i class="icon-xs ms-1"
                                                                             data-feather="play"></i>
                                            </button>

                                            <button type="button"
                                                    class="me-2 mb-2 mb-sm-0 btn btn-primary btn-icon d-inline-flex">
                                                <i class="icon-xs" data-feather="play"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-primary btn-rounded-circle btn-icon d-inline-flex">
                                                <i class="icon-xs" data-feather="play"></i>
                                            </button>
                                        </div>

                                        <div class="code-block mt-4">
                                            <h6>Example</h6>
                                            <button class="btn btn-sm btn-copy-clipboard"
                                                    data-clipboard-target="#buttons-ex-5">Copy
                                            </button>

                                            <pre class="prettyprint lang-html escape" id="buttons-ex-5">
                                                <button type="button" class="me-2 btn btn-primary">Button with icon on right<i
                                                        class="icon-xs ms-1" data-feather="play"></i></button>
                                            </pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons End -->

                    <!-- Cards Start -->
                    <div class="row" id="cards">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Cards</h5>
                                    <p class="sub-header">
                                        Bootstrap's cards provide a flexible and extensible content container with
                                        multiple variants and options. Check out
                                        <a href="https://getbootstrap.com/docs/4.4/components/card/">Bootstrap's Doc</a>
                                        for more examples.
                                    </p>
                                    <div class="row">
                                        <div class="col-lg-5 col-xl-3">
                                            <!-- Simple card -->
                                            <div class="card border">
                                                <img class="card-img-top img-fluid" src="/images/photos/1.jpg"
                                                     alt="Card image cap">
                                                <div class="card-body">
                                                    <h5 class="card-title">Card title</h5>
                                                    <p class="card-text text-muted">
                                                        Some quick example text to build on the card title and make up
                                                        the bulk of the card's content.
                                                    </p>
                                                    <a href="javascript:void(0);" class="btn btn-primary">Button</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col -->

                                        <div class="col-lg-7 col-xl-9">
                                            <div class="code-block">
                                                <h6>Example</h6>

                                                <button class="btn btn-sm btn-copy-clipboard"
                                                        data-clipboard-target="#card-ex-1">Copy
                                                </button>

                                                <pre class="prettyprint lang-html escape" id="card-ex-1">
<div class="card">
    <img class="card-img-top img-fluid" src="/images/photos/1.jpg" alt="Card image cap">
    <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text text-muted">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
        <a href="javascript:void(0);" class="btn btn-primary">Button</a>
    </div>
</div>
                                                </pre>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="row g-0 align-items-center">
                                                    <div class="col-md-5">
                                                        <img src="/images/photos/1.jpg" class="card-img"
                                                             alt="...">
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-0">Card title</h5>
                                                            <p class="card-text text-muted">
                                                                This is a wider card with supporting text lead-in to
                                                                additional content.
                                                            </p>
                                                            <p class="card-text"><small class="text-muted">Last updated
                                                                    3 mins ago</small></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card border">
                                                <div class="row g-0 align-items-center">
                                                    <div class="col-md-7">
                                                        <div class="card-body">
                                                            <h5 class="card-title fs-16">Card title</h5>
                                                            <p class="card-text text-muted">This is a wider card with
                                                                supporting text lead-in to additional content.</p>
                                                            <p class="card-text"><small class="text-muted">Last updated
                                                                    3 mins ago</small></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <img src="/images/photos/1.jpg" class="card-img"
                                                             alt="...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end col -->

                                        <div class="col-lg-6">
                                            <div class="code-block">
                                                <h6>Example</h6>

                                                <button class="btn btn-sm btn-copy-clipboard"
                                                        data-clipboard-target="#card-ex-1">Copy
                                                </button>

                                                <pre class="prettyprint lang-html escape" id="card-ex-1">
<div class="card">
    <div class="row g-0 align-items-center">
        <div class="col-md-5">
            <img src="/images/photos/1.jpg" class="card-img" alt="...">
        </div>
        <div class="col-md-7">
            <div class="card-body">
                <h5 class="card-title mb-0">Card title</h5>
                <p class="card-text text-muted">This is a wider card with supporting text lead-in to additional content.</p>
                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
            </div>
        </div>
    </div>
</div>
                                                </pre>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- Cards End -->

                    <!-- Tabs Start -->
                    <div class="row" id="nav-tabs">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Nav Tabs</h5>
                                    <p class="sub-header">
                                        Takes the basic nav and adds the <code>.nav-tabs</code> class to generate a
                                        tabbed interface.
                                    </p>

                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a href="#home" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <span class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                   data-feather="home"></i></span>
                                                <span class="d-none d-sm-block">Home</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#profile" data-bs-toggle="tab" aria-expanded="true"
                                               class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                   data-feather="user"></i></span>
                                                <span class="d-none d-sm-block">Profile</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#messages" data-bs-toggle="tab" aria-expanded="false"
                                               class="nav-link">
                                                <span class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                   data-feather="mail"></i></span>
                                                <span class="d-none d-sm-block">Messages</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane" id="home">
                                            <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean
                                                commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et
                                                magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis,
                                                ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa
                                                quis enim.</p>
                                            <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate
                                                eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae,
                                                justo. Nullam dictum felis eu pede mollis pretium. Integer
                                                tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                                                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                                eleifend ac, enim.</p>
                                        </div>
                                        <div class="tab-pane show active" id="profile">
                                            <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In
                                                enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam
                                                dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus.
                                                Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean
                                                leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                            <p class="mb-0">Vakal text here dolor sit amet, consectetuer adipiscing
                                                elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                                                penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec
                                                quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla
                                                consequat massa quis enim.</p>
                                        </div>
                                        <div class="tab-pane" id="messages">
                                            <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean
                                                commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et
                                                magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis,
                                                ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa
                                                quis enim.</p>
                                            <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate
                                                eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae,
                                                justo. Nullam dictum felis eu pede mollis pretium. Integer
                                                tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                                                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                                eleifend ac, enim.</p>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard" data-bs-target="#tab-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="tab-ex-1">
                                            <ul class="nav nav-tabs"><li class="nav-item"> <a href="#home"
                                                                                              data-bs-toggle="tab"
                                                                                              aria-expanded="false"
                                                                                              class="nav-link"> <span
                                                            class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                         data-feather="home"></i></span> <span
                                                            class="d-none d-sm-block">Home</span> </a></li><li
                                                    class="nav-item"> <a href="#profile" data-bs-toggle="tab"
                                                                         aria-expanded="true"
                                                                         class="nav-link active"> <span
                                                            class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                         data-feather="user"></i></span> <span
                                                            class="d-none d-sm-block">Profile</span> </a></li><li
                                                    class="nav-item"> <a href="#messages" data-bs-toggle="tab"
                                                                         aria-expanded="false" class="nav-link"> <span
                                                            class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                         data-feather="mail"></i></span> <span
                                                            class="d-none d-sm-block">Messages</span> </a></li></ul> <div
                                                class="tab-content p-3 text-muted"> <div class="tab-pane"
                                                                                         id="home"> ... </div> <div
                                                    class="tab-pane show active" id="profile"> ... </div> <div
                                                    class="tab-pane" id="messages"> ... </div> </div>
                                        </pre>
                                    </div>

                                    <ul class="nav nav-pills navtab-bg nav-justified p-1">
                                        <li class="nav-item">
                                            <a href="#home1" data-bs-toggle="tab" aria-expanded="false"
                                               class="nav-link">
                                                <span class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                   data-feather="home"></i></span>
                                                <span class="d-none d-sm-block">Home</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#profile1" data-bs-toggle="tab" aria-expanded="true"
                                               class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                   data-feather="user"></i></span>
                                                <span class="d-none d-sm-block">Profile</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#messages1" data-bs-toggle="tab" aria-expanded="false"
                                               class="nav-link">
                                                <span class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                   data-feather="mail"></i></span>
                                                <span class="d-none d-sm-block">Messages</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane" id="home1">
                                            <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean
                                                commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et
                                                magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis,
                                                ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa
                                                quis enim.</p>
                                            <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate
                                                eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae,
                                                justo. Nullam dictum felis eu pede mollis pretium. Integer
                                                tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                                                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                                eleifend ac, enim.</p>
                                        </div>
                                        <div class="tab-pane show active" id="profile1">
                                            <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In
                                                enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam
                                                dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus.
                                                Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean
                                                leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                            <p class="mb-0">Vakal text here dolor sit amet, consectetuer adipiscing
                                                elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque
                                                penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec
                                                quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla
                                                consequat massa quis enim.</p>
                                        </div>
                                        <div class="tab-pane" id="messages1">
                                            <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean
                                                commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et
                                                magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis,
                                                ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa
                                                quis enim.</p>
                                            <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate
                                                eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae,
                                                justo. Nullam dictum felis eu pede mollis pretium. Integer
                                                tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                                                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                                eleifend ac, enim.</p>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard" data-bs-target="#tab-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="tab-ex-2">
                                            <ul class="nav nav-pills navtab-bg nav-justified"><li class="nav-item"> <a
                                                        href="#home1" data-bs-toggle="tab" aria-expanded="false"
                                                        class="nav-link"> <span class="d-block d-sm-none"><i
                                                                class="icon icon-xxs"
                                                                data-feather="home"></i></span> <span
                                                            class="d-none d-sm-block">Home</span> </a></li><li
                                                    class="nav-item"> <a href="#profile1" data-bs-toggle="tab"
                                                                         aria-expanded="true"
                                                                         class="nav-link active"> <span
                                                            class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                         data-feather="user"></i></span> <span
                                                            class="d-none d-sm-block">Profile</span> </a></li><li
                                                    class="nav-item"> <a href="#messages1" data-bs-toggle="tab"
                                                                         aria-expanded="false" class="nav-link"> <span
                                                            class="d-block d-sm-none"><i class="icon icon-xxs"
                                                                                         data-feather="mail"></i></span> <span
                                                            class="d-none d-sm-block">Messages</span> </a></li></ul> <div
                                                class="tab-content text-muted"> <div class="tab-pane"
                                                                                     id="home1"> ... </div> <div
                                                    class="tab-pane show active" id="profile1"> ... </div> <div
                                                    class="tab-pane" id="messages1"> ... </div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- Tabs End -->

                    <!-- Carousel Start -->
                    <div class="row" id="carousel">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Carousel</h5>
                                    <p class="sub-header">
                                        A slideshow component for cycling through elements—images or slides of text—like
                                        a carousel.
                                    </p>


                                    <div id="carouselExampleIndicators" class="carousel slide doc-carousel"
                                         data-bs-ride="carousel">
                                        <ol class="carousel-indicators">
                                            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                                                class="active"></li>
                                            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
                                            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
                                        </ol>
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="/images/photos/1.jpg" class="d-block w-100" alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="/images/photos/2.jpg" class="d-block w-100" alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="/images/photos/3.jpg" class="d-block w-100" alt="...">
                                            </div>
                                        </div>
                                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button"
                                           data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button"
                                           data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </a>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#carousel-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="carousel-ex-1">
                                            <div id="carouselExampleIndicators" class="carousel slide"
                                                 data-bs-ride="carousel"> <ol class="carousel-indicators"> <li
                                                        data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                                                        class="active"></li> <li
                                                        data-bs-target="#carouselExampleIndicators"
                                                        data-bs-slide-to="1"> </li> <li
                                                        data-bs-target="#carouselExampleIndicators"
                                                        data-bs-slide-to="2"> </li> </ol> <div class="carousel-inner"> <div
                                                        class="carousel-item active"> <img
                                                            src="/images/photos/2.jpg" class="d-block w-100"
                                                            alt="..."> </div> <div class="carousel-item"> <img
                                                            src="/images/photos/3.jpg" class="d-block w-100"
                                                            alt="..."> </div> <div class="carousel-item"> <img
                                                            src="/images/photos/1.jpg" class="d-block w-100"
                                                            alt="..."> </div> </div> <a class="carousel-control-prev"
                                                                                        href="#carouselExampleIndicators"
                                                                                        role="button"
                                                                                        data-bs-slide="prev"> <span
                                                        class="carousel-control-prev-icon"
                                                        aria-hidden="true"></span> <span class="visually-hidden">Previous</span> </a> <a
                                                    class="carousel-control-next" href="#carouselExampleIndicators"
                                                    role="button" data-bs-slide="next"> <span
                                                        class="carousel-control-next-icon"
                                                        aria-hidden="true"></span> <span
                                                        class="visually-hidden">Next</span> </a> </div>
                                        </pre>
                                    </div>

                                    <p class="sub-header pt-2">
                                        Add captions to your slides easily with the <code class="highlighter-rouge">.carousel-caption</code>
                                        element within any <code class="highlighter-rouge">.carousel-item</code>. They
                                        can be
                                        easily hidden on smaller viewports, as shown below, with optional
                                        <a href="4.3/utilities/display/">display utilities</a>.
                                        We hide them initially with <code class="highlighter-rouge">.d-none</code>
                                        and bring them back on medium-sized devices with <code
                                            class="highlighter-rouge">.d-md-block</code>.
                                    </p>

                                    <div id="carouselExampleCaptions" class="carousel slide doc-carousel"
                                         data-bs-ride="carousel">
                                        <ol class="carousel-indicators">
                                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                                                class="active"></li>
                                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"></li>
                                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"></li>
                                        </ol>
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="/images/photos/1.jpg" class="d-block w-100" alt="...">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <h3 class="text-white">First slide label</h3>
                                                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                                                </div>
                                            </div>
                                            <div class="carousel-item">
                                                <img src="/images/photos/2.jpg" class="d-block w-100" alt="...">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <h3 class="text-white">Second slide label</h3>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                                </div>
                                            </div>
                                            <div class="carousel-item">
                                                <img src="/images/photos/3.jpg" class="d-block w-100" alt="...">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <h3 class="text-white">Third slide label</h3>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl
                                                        consectetur.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button"
                                           data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#carouselExampleCaptions" role="button"
                                           data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </a>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#carousel-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="carousel-ex-2">
                                        <div id="carouselExampleCaptions" class="carousel slide doc-carousel"
                                             data-bs-ride="carousel"> <ol class="carousel-indicators"> <li
                                                    data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                                                    class="active"> </li> <li data-bs-target="#carouselExampleCaptions"
                                                                              data-bs-slide-to="1"></li> <li
                                                    data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"></li> </ol> <div
                                                class="carousel-inner"> <div class="carousel-item active"> <img
                                                        src="/images/photos/1.jpg" class="d-block w-100"
                                                        alt="..."> <div class="carousel-caption d-none d-md-block"> <h5
                                                            class="text-white">First slide label</h5> <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p> </div> </div> <div
                                                    class="carousel-item"> <img src="/images/photos/2.jpg"
                                                                                class="d-block w-100" alt="..."> <div
                                                        class="carousel-caption d-none d-md-block"> <h5
                                                            class="text-white">Second slide label</h5> <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> </div> </div> <div
                                                    class="carousel-item"> <img src="/images/photos/3.jpg"
                                                                                class="d-block w-100" alt="..."> <div
                                                        class="carousel-caption d-none d-md-block"> <h5
                                                            class="text-white">Third slide label</h5> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p> </div> </div> </div> <a
                                                class="carousel-control-prev" href="#carouselExampleCaptions"
                                                role="button" data-bs-slide="prev"> <span
                                                    class="carousel-control-prev-icon" aria-hidden="true"></span> <span
                                                    class="visually-hidden>Previous</span> </a> <a class="
                                                    carousel-control-next" href="#carouselExampleCaptions" role="button" data-bs-slide="next"> <span
                                                    class="carousel-control-next-icon" aria-hidden="true"></span> <span
                                                    class="visually-hidden">Next</span> </a> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Carousel End -->

                    <!-- Dropdown Start -->
                    <div class="row" id="dropdowns">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Dropdowns</h5>
                                    <p class="sub-header">
                                        Toggle contextual overlays for displaying lists of links and more with the
                                        Bootstrap dropdown plugin.
                                    </p>

                                    <div class="dropdown me-2 d-sm-inline-flex mb-2 mb-sm-0">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Dropdown button <i class="icon"><span
                                                    data-feather="chevron-down"></span></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Separated link</a>
                                        </div>
                                    </div>

                                    <div class="btn-group dropdown">
                                        <button type="button" class="btn btn-secondary">Split Button Dropdown</button>
                                        <button type="button"
                                                class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                                id="dropdownMenuReference" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false" data-reference="parent">
                                            <i class="icon"><span data-feather="chevron-down"></span></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuReference">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Separated link</a>
                                        </div>
                                    </div>
                                    <!-- /btn-group -->

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>

                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#dropdown-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="dropdown-ex-1">
                                            <div class="btn-group me-1"> <button type="button"
                                                                                 class="btn btn-primary dropdown-toggle"
                                                                                 data-bs-toggle="dropdown"
                                                                                 aria-haspopup="true"
                                                                                 aria-expanded="false">Dropdown <i
                                                        class="icon"><span
                                                            data-feather="chevron-down"></span></i></button> <div
                                                    class="dropdown-menu"> <a class="dropdown-item" href="#">Action</a> <a
                                                        class="dropdown-item" href="#">Another action</a> <a
                                                        class="dropdown-item" href="#">Something else here</a> <div
                                                        class="dropdown-divider"></div> <a class="dropdown-item"
                                                                                           href="#">Separated link</a> </div> </div>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">Dropdown menu position variations</p>

                                    <div class="d-sm-inline-flex me-1 dropdown mb-2 mb-sm-0">
                                        <button type="button" class="btn btn-success dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Right Align <i class="icon"><span data-feather="chevron-down"></span></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                    <div class="btn-group me-1 dropend mb-2 mb-sm-0">
                                        <button type="button" class="btn btn-info dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Drop Right <i class="icon"><span data-feather="chevron-right"></span></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Separated link</a>
                                        </div>
                                    </div>
                                    <div class="btn-group dropstart me-1">
                                        <button type="button" class="btn btn-danger dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon"><span data-feather="chevron-left"></span></i> Drop Left
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Separated link</a>
                                        </div>
                                    </div>

                                    <div class="dropdown hovered me-2 d-inline-flex">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            On Hover <i class="icon"><span data-feather="chevron-down"></span></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Separated link</a>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#dropdown-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="dropdown-ex-2">
                                            <div class="btn-group me-1"> <button type="button"
                                                                                 class="btn btn-success dropdown-toggle"
                                                                                 data-bs-toggle="dropdown"
                                                                                 aria-haspopup="true"
                                                                                 aria-expanded="false"> Right Align <i
                                                        class="icon"><span
                                                            data-feather="chevron-down"></span></i> </button> <div
                                                    class="dropdown-menu dropdown-menu-right"> <a class="dropdown-item"
                                                                                                  href="#">Action</a> <a
                                                        class="dropdown-item" href="#">Another action</a> <a
                                                        class="dropdown-item"
                                                        href="#">Something else here</a> </div> </div>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        You can put a form or simple text within a dropdown menu or set the different
                                        position
                                    </p>

                                    <div class="btn-group dropdown me-1 mb-2 mb-sm-0">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownMenutext" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Simple text <i class="icon"><span data-feather="chevron-down"></span></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-lg p-3"
                                             aria-labelledby="dropdownMenutext">
                                            <div class="text-muted">
                                                <p>Some example text that's free-flowing within the dropdown menu.</p>
                                                <p class="mb-0">And this is more example text.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group dropdown me-1">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Dropdown
                                            menu Forms <i class="icon"><span data-feather="chevron-down"></span></i>
                                        </button>
                                        <form class="dropdown-menu dropdown-menu-lg p-3">
                                            <div class="mb-3">
                                                <label for="exampleDropdownFormEmail" class="form-label">Email
                                                    address</label>
                                                <input type="email" class="form-control" id="exampleDropdownFormEmail"
                                                       placeholder="email@example.com">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleDropdownFormPassword"
                                                       class="form-label">Password</label>
                                                <input type="password" class="form-control"
                                                       id="exampleDropdownFormPassword" placeholder="Password">
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="dropdownCheck2">
                                                    <label class="form-check-label" for="dropdownCheck2">Remember
                                                        me</label>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Sign in</button>
                                        </form>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#dropdown-ex-3">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="dropdown-ex-3">
                                            <div class="btn-group dropdown me-1"> <button
                                                    class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">Dropdown menu Forms <i class="icon"><span
                                                            data-feather="chevron-down"></span></i> </button> <form
                                                    class="dropdown-menu dropdown-lg p-3"> <div class="mb-3"> <label
                                                            for="exampleDropdownFormEmail2" class="form-label">Email address</label> <input
                                                            type="email" class="form-control"
                                                            id="exampleDropdownFormEmail2"
                                                            placeholder="email@example.com"> </div> <div
                                                        class="mb-3"> <label for="exampleDropdownFormPassword2"
                                                                             class="form-label">Password</label> <input
                                                            type="password" class="form-control"
                                                            id="exampleDropdownFormPassword2"
                                                            placeholder="Password"> </div> <div class="mb-3"> <div
                                                            class="form-check"> <input type="checkbox"
                                                                                       class="form-check-input"
                                                                                       id="dropdownCheck"> <label
                                                                class="form-check-label"
                                                                for="dropdownCheck">Remember me </label> </div> </div> <button
                                                        type="submit" class="btn btn-primary">Sign in</button> </form> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- Dropdown End -->

                    <!-- Forms Start -->
                    <div class="row" id="forms">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Form Elements</h5>
                                    <p class="sub-header">
                                        Examples and usage guidelines for form control styles, layout options, and
                                        custom components for creating a wide variety of forms.
                                    </p>


                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="default-1">Text Input</label>
                                                <input type="text" class="form-control" id="default-1"
                                                       placeholder="A text input"/>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="default-5">Password Input</label>
                                                <input type="password" class="form-control" id="default-5"
                                                       placeholder="A password input" value="12345678"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="default-textarea">Textarea</label>
                                                <textarea class="form-control" id="default-textarea" rows="5">text are content goes here...</textarea>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="default-3">Default Select</label>
                                                <select class="form-select" id="default-3">
                                                    <option value="default_option">Default Option</option>
                                                    <option value="option_select_name">Option select name</option>
                                                    <option value="option_select_name">Option select name</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="default-2">File Upload</label>
                                                <div class="">
                                                    <label class="form-label" for="customFile">Choose file</label>
                                                    <input type="file" multiple="" class="form-control"
                                                           id="customFile"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="default-4">Default Select Multiple</label>
                                            <select class="form-select" id="default-4" multiple="">
                                                <option value="option_select0">Default Option</option>
                                                <option value="option_select1">Option select name</option>
                                                <option value="option_select2">Option select name</option>
                                                <option value="option_select2">Option select name</option>
                                                <option value="option_select2">Option select name</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="default7">Input with icon</label>
                                                <div class="form-control-with-hint">
                                                    <input type="text" class="form-control" id="default7"
                                                           placeholder="Input placeholder"/>
                                                    <span class="form-control-feedback"><i data-feather="search"
                                                                                           class="icon-xs"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="default-8">Input with hint text</label>
                                                <div class="form-control-with-hint">
                                                    <input type="text" class="form-control" id="default-8"
                                                           placeholder="Input placeholder"/>
                                                    <span class="form-control-feedback">USD</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>

                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#form-ex-1">
                                            Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="form-ex-1">
                                            <div class="row"> <div class="col-sm-6"> <div class="mb-3"> <label
                                                            class="form-label" for="default-1">Text Input</label> <input
                                                            type="text" class="form-control" id="default-1"
                                                            placeholder="A text input"/> </div> <div
                                                        class="mb-3"> <label class="form-label" for="default-5">Password Input</label> <input
                                                            type="password" class="form-control" id="default-5"
                                                            placeholder="A password input"
                                                            value="12345678"/> </div> </div> <div class="col-sm-6"> <div
                                                        class="mb-3"><label class="form-label" for="default-textarea">Textarea</label> <textarea
                                                            class="form-control" id="default-textarea" rows="5">text are content goes here...</textarea> </div> </div> <div
                                                    class="col-sm-6"> <div class="mb-3"> <label class="form-label"
                                                                                                for="default-3">Default Select</label> <select
                                                            class="form-control" id="default-3"> <option
                                                                value="default_option">Default Option</option> <option
                                                                value="option_select_name">Option select name</option> <option
                                                                value="option_select_name">Option select name</option> </select> </div> <div
                                                        class="mb-3"> <label class="form-label" for="default-2">File Upload</label> <div
                                                            class="custom-file"> <input type="file" multiple=""
                                                                                        class="custom-file-input"
                                                                                        id="customFile"/> <label
                                                                class="custom-file-label"
                                                                for="customFile">Choose file</label> </div> </div> </div> <div
                                                    class="col-sm-6"> <label class="form-label" for="default-4">Default Select Multiple</label> <select
                                                        class="form-select" id="default-4" multiple=""> <option
                                                            value="option_select0">Default Option</option> <option
                                                            value="option_select1">Option select name</option> <option
                                                            value="option_select2">Option select name</option> <option
                                                            value="option_select2">Option select name</option> <option
                                                            value="option_select2">Option select name</option> </select> </div> <div
                                                    class="col-sm-6"> <div class="mb-3"> <label class="form-label"
                                                                                                for="default-7">Input with icon</label> <div
                                                            class="form-control-with-hint"> <input type="text"
                                                                                                   class="form-control"
                                                                                                   id="default-7"
                                                                                                   placeholder="Input placeholder"/> <span
                                                                class="form-control-feedback"><i data-feather="search"
                                                                                                 class="icon-xs"></i></span> </div> </div> </div> <div
                                                    class="col-sm-6"> <div class="mb-3"> <label class="form-label"
                                                                                                for="default-8">Input with hint text</label> <div
                                                            class="form-control-with-hint"> <input type="text"
                                                                                                   class="form-control"
                                                                                                   id="default-8"
                                                                                                   placeholder="Input placeholder"/> <span
                                                                class="form-control-feedback">USD</span> </div> </div> </div> </div>
                                        </pre>
                                    </div>

                                    <p class="sub-header pt-2">
                                        Set heights using classes like <code>.form-control-lg</code> and
                                        <code>.form-control-sm</code>.
                                    </p>
                                    <div class="row">
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <input class="form-control form-control-lg" type="text"
                                                   placeholder=".form-control-lg">
                                        </div>
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <input class="form-control" type="text" placeholder="Default input">
                                        </div>
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <input class="form-control form-control-sm" type="text"
                                                   placeholder=".form-control-sm">
                                        </div>
                                    </div>

                                    <p class="sub-header pt-4">
                                        Custom controls including Checkboxes, Radios, Select, Range, etc.
                                    </p>

                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="customcheck1" name="customRadioInline1"
                                                       class="form-check-input">
                                                <label class="form-check-label" for="customcheck1">Check this custom
                                                    checkbox</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" id="customcheck2" name="customRadioInline1"
                                                       class="form-check-input">
                                                <label class="form-check-label" for="customcheck2">Check this custom
                                                    checkbox 2</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="radio" id="customRadioInline1" name="customRadioInline1"
                                                       class="form-check-input">
                                                <label class="form-check-label" for="customRadioInline1">Toggle this
                                                    custom radio</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" id="customRadioInline2" name="customRadioInline1"
                                                       class="form-check-input">
                                                <label class="form-check-label" for="customRadioInline2">Or toggle
                                                    this other custom radio</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3 align-items-center">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <select class="form-select" id="form-select">
                                                <option selected>Open this select menu</option>
                                                <option value="1">One</option>
                                                <option value="2">Two</option>
                                                <option value="3">Three</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="range" class="form-range" id="customRange1"/>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#form-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="form-ex-2">
                                            <div class="row"> <div class="col"> <div
                                                        class="form-control form-check"> <input type="checkbox"
                                                                                                id="customcheck1"
                                                                                                name="customRadioInline1"
                                                                                                class="form-check-input"> <label
                                                            class="form-check-label" for="customcheck1">Check this custom checkbox</label> </div> <div
                                                        class="form-control form-check"> <input type="checkbox"
                                                                                                id="customcheck2"
                                                                                                name="customRadioInline1"
                                                                                                class="form-check-input"> <label
                                                            class="form-check-label" for="customcheck2">Check this custom checkbox 2</label> </div> </div> <div
                                                    class="col"> <div class="form-check"> <input type="radio"
                                                                                                 id="customRadioInline1"
                                                                                                 name="customRadioInline1"
                                                                                                 class="form-check-input"> <label
                                                            class="form-check-label" for="customRadioInline1">Toggle this custom radio</label> </div> <div
                                                        class="form-check"> <input type="radio" id="customRadioInline2"
                                                                                   name="customRadioInline1"
                                                                                   class="form-check-input"> <label
                                                            class="form-check-label" for="customRadioInline2">Or toggle this other custom radio</label> </div> </div> </div> <div
                                                class="row mt-3 align-items-center"> <div class="col"> <select
                                                        class="form-select" id="form-select"> <option selected>Open this select menu</option> <option
                                                            value="1">One</option> <option value="2">Two</option> <option
                                                            value="3">Three</option> </select> </div> <div class="col"> <input
                                                        type="range" class="custom-range"
                                                        id="customRange1"/> </div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Forms End -->

                    <!-- Modal Start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="modals">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Modals</h5>
                                    <p class="sub-header">
                                        A modal plugin allows to add dialogs to your site for lightboxes, user
                                        notifications, or completely custom content, etc.
                                    </p>

                                    <!-- sample modal content -->
                                    <div id="myModal" class="modal fade" tabindex="-1" role="dialog"
                                         aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel">Add more storage</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5>You are out of storage space.</h5>
                                                    <p>To upload more data, please add additional storage space.</p>
                                                    <form class="d-flex text-align-center">
                                                        <label class="my-auto me-2" for="selectSize">Select
                                                            Size: </label>
                                                        <select class="form-select my-1 me-sm-2 w-50" id="selectSize">
                                                            <option selected>Choose...</option>
                                                            <option value="1">1 GB</option>
                                                            <option value="10">10 GB</option>
                                                            <option value="50">50 GB</option>
                                                            <option value="100">100 GB</option>
                                                            <option value="500">500 GB</option>
                                                            <option value="1000">1 TB</option>
                                                        </select>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">
                                                        Cancel
                                                    </button>
                                                    <button type="button" class="btn btn-primary">Upgrade</button>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->

                                    <div class="modal fade" id="bs-example-modal-xl" tabindex="-1" role="dialog"
                                         aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myExtraLargeModalLabel">Extra large
                                                        modal</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.modal -->

                                    <!--  Modal content for the above example -->
                                    <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog"
                                         aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ...
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->

                                    <div class="modal fade" id="bs-example-modal-sm" tabindex="-1" role="dialog"
                                         aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="mySmallModalLabel">Small modal</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ...
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->

                                    <div class="modal fade" id="centermodal" tabindex="-1" role="dialog"
                                         aria-labelledby="myCenterModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myCenterModalLabel">Center modal</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5>Overflowing text to show scroll behavior</h5>
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p class="mb-0">Praesent commodo cursus magna, vel scelerisque nisl
                                                        consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum
                                                        faucibus dolor auctor.</p>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->

                                    <!-- Long Content Scroll Modal -->
                                    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
                                         aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalScrollableTitle">Modal title
                                                    </h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur
                                                        et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                        dolor auctor.</p>
                                                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo
                                                        cursus magna, vel scelerisque nisl consectetur et. Donec sed
                                                        odio dui. Donec ullamcorper nulla non metus auctor
                                                        fringilla.</p>
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur
                                                        et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                        dolor auctor.</p>
                                                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo
                                                        cursus magna, vel scelerisque nisl consectetur et. Donec sed
                                                        odio dui. Donec ullamcorper nulla non metus auctor
                                                        fringilla.</p>
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur
                                                        et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                        dolor auctor.</p>
                                                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo
                                                        cursus magna, vel scelerisque nisl consectetur et. Donec sed
                                                        odio dui. Donec ullamcorper nulla non metus auctor
                                                        fringilla.</p>
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur
                                                        et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                        dolor auctor.</p>
                                                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo
                                                        cursus magna, vel scelerisque nisl consectetur et. Donec sed
                                                        odio dui. Donec ullamcorper nulla non metus auctor
                                                        fringilla.</p>
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur
                                                        et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                        dolor auctor.</p>
                                                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo
                                                        cursus magna, vel scelerisque nisl consectetur et. Donec sed
                                                        odio dui. Donec ullamcorper nulla non metus auctor
                                                        fringilla.</p>
                                                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo
                                                        odio, dapibus ac facilisis in, egestas eget quam. Morbi leo
                                                        risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur
                                                        et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                        dolor auctor.</p>
                                                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo
                                                        cursus magna, vel scelerisque nisl consectetur et. Donec sed
                                                        odio dui. Donec ullamcorper nulla non metus auctor
                                                        fringilla.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.modal -->

                                    <div class="modal fade" id="modal-success" tabindex="-1" role="dialog"
                                         aria-labelledby="modal-successLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center pt-0">
                                                    <span class="icon icon-xl text-success">
                                                        @svg('/duotone-icons/communication/Mail-attachment')
                                                    </span>
                                                    <h4 class="text-success mt-0">Awesome!</h4>
                                                    <p class="mx-auto text-muted">We receieved your application and will
                                                        process it shortly.</p>
                                                    <div class="mt-4">
                                                        <a href="#" class="btn btn-white btn-sm"
                                                           data-bs-dismiss="modal">
                                                            <i data-feather="arrow-left" class="icon-xxs me-1"></i>Back
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->

                                    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog"
                                         aria-labelledby="modal-errorLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center pt-0">
                                                    <span class="icon icon-xl text-danger">
                                                        @svg('/duotone-icons/general/Sad')
                                                    </span>
                                                    <h4 class="text-danger mt-0">Something went wrong.</h4>
                                                    <p class="mx-auto text-muted mt-2">
                                                        We are unable to process your request at the moment. Our
                                                        appologies, try back in about 5 minutes.
                                                    </p>
                                                    <div class="mt-4">
                                                        <a href="#" class="btn btn-white btn-sm"
                                                           data-bs-dismiss="modal">
                                                            <i data-feather="arrow-left" class="icon-xxs me-1"></i>Back
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->


                                    <div class="row">
                                        <div class="col">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-soft-primary mb-2 mb-sm-0"
                                                    data-bs-toggle="modal" data-bs-target="#myModal">Standard modal
                                            </button>

                                            <!-- Extra large modal -->
                                            <button type="button"
                                                    class="btn btn-soft-secondary ms-0 ms-sm-5 mb-2 mb-sm-0"
                                                    data-bs-toggle="modal" data-bs-target="#bs-example-modal-xl">Extra
                                                large
                                            </button>

                                            <!-- Large modal -->
                                            <button type="button" class="btn btn-soft-success ms-0 ms-sm-2"
                                                    data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg">Large
                                            </button>

                                            <!-- Small modal -->
                                            <button type="button" class="btn btn-soft-info ms-2" data-bs-toggle="modal"
                                                    data-bs-target="#bs-example-modal-sm">Small
                                            </button>

                                        </div>
                                        <!-- Col End -->
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard" data-bs-target="#modal-ex-1">
                                            Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="modal-ex-1">
                                            <div id="myModal" class="modal fade" tabindex="-1" role="dialog"
                                                 aria-labelledby="myModalLabel" aria-hidden="true"> <div
                                                    class="modal-dialog"> <div class="modal-content"> <div
                                                            class="modal-header"> <h4 class="modal-title"
                                                                                      id="myModalLabel">Add more storage</h4> <button
                                                                type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"> </button> </div> <div
                                                            class="modal-body"> <h5>You are out of storage space.</h5> <p>To upload more data, please add additional storage space.</p> <form
                                                                class="form-inline"> <label class="my-1 me-2"
                                                                                            for="selectSize">Select Size: </label> <select
                                                                    class="form-select my-1 me-sm-2 w-50"
                                                                    id="selectSize"> <option selected>Choose...</option> <option
                                                                        value="1">1 GB</option> <option
                                                                        value="10">10 GB</option> <option value="50">50 GB</option> <option
                                                                        value="100">100 GB</option> <option value="500">500 GB</option> <option
                                                                        value="1000">1 TB</option> </select> </form> </div> <div
                                                            class="modal-footer"> <button type="button"
                                                                                          class="btn btn-white"
                                                                                          data-bs-dismiss="modal">Cancel</button> <button
                                                                type="button" class="btn btn-primary">Upgrade</button> </div> </div> </div> </div>
                                        </pre>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col">
                                            <p class="sub-header">
                                                Add <code>.modal-dialog-centered</code> to <code>.modal-dialog</code> to
                                                vertically center the modal.
                                            </p>
                                            <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                                                    data-bs-target="#centermodal">Vertically center
                                            </button>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard" data-bs-target="#modal-ex-2">
                                            Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="modal-ex-2">
                                            <div class="modal fade" id="centermodal" tabindex="-1" role="dialog"
                                                 aria-labelledby="myCenterModalLabel" aria-hidden="true"> <div
                                                    class="modal-dialog modal-dialog-centered"> <div
                                                        class="modal-content"> <div class="modal-header"> <h4
                                                                class="modal-title"
                                                                id="myCenterModalLabel">Center modal</h4> <button
                                                                type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"> </button> </div> <div
                                                            class="modal-body"> <h5>Overflowing text to show scroll behavior</h5> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p
                                                                class="mb-0">Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> </div> </div> </div> </div>
                                        </pre>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col">
                                            <p class="sub-header">
                                                You can also create a scrollable modal that allows scroll the modal body
                                                by adding
                                                <code>.modal-dialog-scrollable</code> to <code>.modal-dialog</code>.
                                            </p>
                                            <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModalScrollable">Scrollable
                                            </button>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard" data-bs-target="#modal-ex-3">
                                            Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="modal-ex-3">
                                            <div class="modal fade" id="exampleModalScrollable" tabindex="-1"
                                                 role="dialog" aria-labelledby="exampleModalScrollableTitle"
                                                 aria-hidden="true"> <div class="modal-dialog modal-dialog-scrollable"
                                                                          role="document"> <div class="modal-content"> <div
                                                            class="modal-header"> <h4 class="modal-title"
                                                                                      id="exampleModalScrollableTitle">Modal title </h4> <button
                                                                type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"> </button> </div> <div
                                                            class="modal-body"> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla. </p> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla. </p> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla. </p> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla. </p> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla. </p> <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p> <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p> <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla. </p> </div> <div
                                                            class="modal-footer"> <button type="button"
                                                                                          class="btn btn-white"
                                                                                          data-bs-dismiss="modal">Close</button> <button
                                                                type="button"
                                                                class="btn btn-primary">Save changes</button> </div> </div> </div> </div>
                                        </pre>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col">
                                            <p class="sub-header">
                                                A modal can be used to show contexual messages including success, error,
                                                warning, information messages, etc.
                                            </p>
                                            <button type="button" class="btn btn-soft-success ms-2"
                                                    data-bs-toggle="modal" data-bs-target="#modal-success">Success
                                            </button>
                                            <button type="button" class="btn btn-soft-danger ms-2"
                                                    data-bs-toggle="modal" data-bs-target="#modal-error">Error
                                            </button>
                                        </div>
                                    </div>
                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard" data-bs-target="#modal-ex-4">
                                            Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="modal-ex-4">
                                            <div class="modal fade" id="modal-success" tabindex="-1" role="dialog"
                                                 aria-labelledby="modal-successLabel" aria-hidden="true"><div
                                                    class="modal-dialog modal-dialog-centered modal-sm"><div
                                                        class="modal-content"><div
                                                            class="modal-header border-bottom-0 pb-0"> <button
                                                                type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"> </button></div><div
                                                            class="modal-body text-center pt-0"> <span
                                                                class="icon icon-xl text-success">
                                                                @svg('/duotone-icons/communication/Mail-attachment') </span><h4
                                                                class="text-success mt-0">Awesome!</h4><p
                                                                class="mx-auto text-muted">We receieved your application and will process it shortly.</p><div
                                                                class="mt-4"> <a href="#" class="btn btn-white btn-sm"
                                                                                 data-bs-dismiss="modal"><i
                                                                        data-feather="arrow-left"
                                                                        class="icon-xxs me-1"></i>Back</a></div></div></div></div></div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->

                    <!-- Progressbars Start -->
                    <div class="row" id="progressbars">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Progress</h5>
                                    <p class="sub-header">
                                        Bootstrap custom progress bars featuring support for stacked bars, animated
                                        backgrounds, and text labels
                                    </p>
                                    <div>
                                        <div class="progress mb-3">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="0"
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress mb-3">
                                            <div class="progress-bar" role="progressbar" style="width: 33%"
                                                 aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 66%"
                                                 aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"
                                                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 25%;"
                                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#progress-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="progress-ex-1">
                                            <div class="progress"> <div class="progress-bar" role="progressbar"
                                                                        style="width: 25%;" aria-valuenow="25"
                                                                        aria-valuemin="0" aria-valuemax="100">25%</div> </div>
                                        </pre>
                                    </div>

                                    <div class="mt-5">
                                        <p class="sub-header">
                                            Add <code>.progress-bar-striped</code> to any <code>.progress-bar</code> to
                                            apply a stripe via CSS gradient over the progress bar's background color.
                                            Additionally you can add <code>.progress-bar-animated</code> to make it
                                            animated as well.
                                        </p>

                                        <div>
                                            <div class="progress mb-3">
                                                <div class="progress-bar progress-bar-striped" role="progressbar"
                                                     style="width: 10%" aria-valuenow="10" aria-valuemin="0"
                                                     aria-valuemax="100"></div>
                                            </div>

                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                     role="progressbar" aria-valuenow="75" aria-valuemin="0"
                                                     aria-valuemax="100" style="width: 75%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#progress-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="progress-ex-2">
                                            <div class="progress"> <div
                                                    class="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar" aria-valuenow="75" aria-valuemin="0"
                                                    aria-valuemax="100" style="width: 75%"></div> </div>
                                        </pre>
                                    </div>

                                    <div class="mt-5">
                                        <p class="sub-header">
                                            Set a height value on the <code>.progress</code>. The inner <code>.progress-bar</code>
                                            will automatically resize accordingly.
                                        </p>

                                        <div>
                                            <div class="progress mb-3" style="height: 2px;">
                                                <div class="progress-bar" role="progressbar" style="width: 25%;"
                                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="progress" style="height: 16px;">
                                                <div class="progress-bar" role="progressbar" style="width: 25%;"
                                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#progress-ex-3">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="progress-ex-3">
                                            <div class="progress" style="height: 16px;"> <div class="progress-bar"
                                                                                              role="progressbar"
                                                                                              style="width: 25%;"
                                                                                              aria-valuenow="25"
                                                                                              aria-valuemin="0"
                                                                                              aria-valuemax="100"></div> </div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progressbars End -->

                    <!-- Pagination Start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="pagination">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Pagination</h5>
                                    <p class="sub-header">
                                        Examples for showing pagination to indicate a series of related content exists
                                        across multiple pages
                                    </p>

                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                        </ul>
                                    </nav>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#pagination-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="pagination-ex-1">
                                            <nav aria-label="Page navigation example"> <ul class="pagination"> <li
                                                        class="page-item"><a class="page-link"
                                                                             href="#">Previous</a> </li> <li
                                                        class="page-item"><a class="page-link" href="#">1</a></li> <li
                                                        class="page-item"><a class="page-link" href="#">2</a></li> <li
                                                        class="page-item"><a class="page-link" href="#">3</a></li> <li
                                                        class="page-item"><a class="page-link"
                                                                             href="#">Next</a></li> </ul> </nav>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        You can use icon instead of showing text label for previous and next actions
                                    </p>

                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#pagination-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="pagination-ex-2">
                                        <nav aria-label="Page navigation example"> <ul class="pagination"> <li
                                                    class="page-item"><a class="page-link" href="#"
                                                                         aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li> <li
                                                    class="page-item"><a class="page-link" href="#">1</a></li> <li
                                                    class="page-item"><a class="page-link" href="#">2</a></li> <li
                                                    class="page-item"><a class="page-link" href="#">3</a></li> <li
                                                    class="page-item"><a class="page-link" href="#"
                                                                         aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        Just add class modifier <code>.pagination-rounded</code> to
                                        <code>.pagination</code> in order to have rounded page action link
                                    </p>

                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination pagination-rounded">
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#pagination-ex-3">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="pagination-ex-3">
                                        <nav aria-label="Page navigation example"> <ul
                                                class="pagination pagination-rounded"> <li class="page-item"><a
                                                        class="page-link" href="#" aria-label="Previous"><span
                                                            aria-hidden="true">&laquo;</span></a></li> <li
                                                    class="page-item"><a class="page-link" href="#">1</a></li> <li
                                                    class="page-item"><a class="page-link" href="#">2</a></li> <li
                                                    class="page-item"><a class="page-link" href="#">3</a></li> <li
                                                    class="page-item"><a class="page-link" href="#"
                                                                         aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li> </ul> </nav>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pagination End -->

                    <!-- spinners -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="spinners">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Spinners</h5>
                                    <p class="sub-header">
                                        Indicate the loading state of a component or page with Bootstrap spinners, built
                                        entirely with HTML, CSS, and no JavaScript.
                                    </p>

                                    <div>
                                        <div class="spinner-border text-primary m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-border text-secondary m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-border text-success m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-border text-danger m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-border text-warning m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-border text-info m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#spinner-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="spinner-ex-1">
                                            <div class="spinner-border text-primary m-2" role="status"><span
                                                    class="visually-hidden">Loading...</span></div>
                                        </pre>
                                    </div>

                                    <p class="sub-header mt-4">
                                        If you don't fancy a border spinner, switch to the grow spinner. While it
                                        doesn't technically spin, it does repeatedly grow!
                                    </p>

                                    <div>
                                        <div class="spinner-grow text-primary m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-grow text-secondary m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-grow text-success m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-grow text-danger m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-grow text-warning m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="spinner-grow text-info m-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>

                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#spinner-ex-2">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="spinner-ex-2">
                                            <div class="spinner-grow text-primary m-2" role="status"><span
                                                    class="visually-hidden">Loading...</span></div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <!-- Offcanvas Start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="offcanvas">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Offcanvas</h5>
                                    <p class="sub-header">
                                        Use the buttons below to show and hide an offcanvas element via JavaScript that
                                        toggles the
                                        <code>.show</code> class on an element with the <code>.offcanvas</code> class.
                                    </p>

                                    <button class="btn btn-soft-primary me-1 mb-2 mb-xl-0" type="button"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop"
                                            aria-controls="offcanvasExample">
                                        Top Offcanvas
                                    </button>
                                    <button class="btn btn-soft-primary me-1 mb-2 mb-xl-0" type="button"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom"
                                            aria-controls="offcanvasExample">
                                        Bottom Offcanvas
                                    </button>
                                    <button class="btn btn-soft-primary me-1 mb-2 mb-xl-0" type="button"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                                            aria-controls="offcanvasExample">
                                        Left Offcanvas
                                    </button>
                                    <button class="btn btn-soft-primary me-1 mb-2 mb-xl-0" type="button"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft"
                                            aria-controls="offcanvasExample">
                                        Right Offcanvas
                                    </button>


                                    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop"
                                         aria-labelledby="offcanvasExampleLabel">
                                        <div class="offcanvas-header">
                                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <div>Some text as placeholder. In real life you can have the elements you
                                                have chosen. Like, text, images, lists, etc.
                                            </div>
                                            <div class="dropdown mt-3">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-bs-toggle="dropdown">Dropdown
                                                    button
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom"
                                         aria-labelledby="offcanvasExampleLabel">
                                        <div class="offcanvas-header">
                                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <div>Some text as placeholder. In real life you can have the elements you
                                                have chosen. Like, text, images, lists, etc.
                                            </div>
                                            <div class="dropdown mt-3">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-bs-toggle="dropdown">Dropdown
                                                    button
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasRight"
                                         aria-labelledby="offcanvasExampleLabel">
                                        <div class="offcanvas-header">
                                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <div>Some text as placeholder. In real life you can have the elements you
                                                have chosen. Like, text, images, lists, etc.
                                            </div>
                                            <div class="dropdown mt-3">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-bs-toggle="dropdown">Dropdown
                                                    button
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLeft"
                                         aria-labelledby="offcanvasExampleLabel">
                                        <div class="offcanvas-header">
                                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                                            <button type="button" class="btn-close text-reset"
                                                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <div>Some text as placeholder. In real life you can have the elements you
                                                have chosen. Like, text, images, lists, etc.
                                            </div>
                                            <div class="dropdown mt-3">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-bs-toggle="dropdown">Dropdown
                                                    button
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>

                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#offcanvas-ex-4">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="offcanvas-ex-4">
                                            <button class="btn btn-soft-primary me-1" type="button"
                                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft"
                                                    aria-controls="offcanvasExample">Left Offcanvas</button><div
                                                class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLeft"
                                                aria-labelledby="offcanvasExampleLabel"><div
                                                    class="offcanvas-header"><h5 class="offcanvas-title"
                                                                                 id="offcanvasExampleLabel">Offcanvas</h5><button
                                                        type="button" class="btn-close text-reset"
                                                        data-bs-dismiss="offcanvas" aria-label="Close"></button></div><div
                                                    class="offcanvas-body"><div>Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.</div><div
                                                        class="dropdown mt-3"><button
                                                            class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-bs-toggle="dropdown">Dropdown button</button><ul
                                                            class="dropdown-menu" aria-labelledby="dropdownMenuButton"><li><a
                                                                    class="dropdown-item" href="#">Action</a></li><li><a
                                                                    class="dropdown-item"
                                                                    href="#">Another action</a></li><li><a
                                                                    class="dropdown-item"
                                                                    href="#">Something else here</a></li></ul></div></div></div>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Offcanvas End -->

                    <!-- Popovers Start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="popovers">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Popovers</h5>
                                    <p class="sub-header">Add small overlays of content, like those on the iPad, to any
                                        element for housing secondary information.</p>

                                    <div class="button-list" id="popover-container">
                                        <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                data-bs-container="#popover-container" title="" data-bs-toggle="popover"
                                                data-bs-placement="top"
                                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."
                                                data-original-title="">Popover on top
                                        </button>

                                        <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                data-bs-container="#popover-container" title="" data-bs-toggle="popover"
                                                data-bs-placement="bottom"
                                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."
                                                data-original-title="">Popover on bottom
                                        </button>

                                        <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                data-bs-container="#popover-container" title="" data-bs-toggle="popover"
                                                data-bs-placement="right"
                                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."
                                                data-original-title="">Popover on right
                                        </button>

                                        <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                data-bs-container="#popover-container" title="" data-bs-toggle="popover"
                                                data-bs-placement="left"
                                                data-bs-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."
                                                data-original-title="Popover title">Popover on left
                                        </button>

                                        <button type="button" tabindex="0" class="btn btn-primary me-1"
                                                data-bs-toggle="popover" data-bs-trigger="focus" title=""
                                                data-bs-content="And here's some amazing content. It's very engaging. Right?"
                                                data-original-title="Dismissible popover">Dismissible popover
                                        </button>
                                    </div>

                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#popover-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="popover-ex-1">
                                            <button type="button" tabindex="0" class="btn btn-primary me-1"
                                                    data-bs-toggle="popover" data-bs-trigger="focus" title=""
                                                    data-bs-content="And here's some amazing content. It's very engaging. Right?"
                                                    data-original-title="Dismissible popover">Dismissible popover</button>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Popovers End -->

                    <!-- Tooltips Start-->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="tooltips">
                                <div class="card-body">
                                    <h5 class="card-title mb-0">Tooltips</h5>
                                    <p class="sub-header">Examples for adding custom Bootstrap tooltips with CSS and
                                        JavaScript using CSS3 for animations and data-attributes for local title
                                        storage.</p>

                                    <div class="button-list">
                                        <span id="tooltip-container-top">
                                            <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                    data-bs-container="#tooltip-container-top" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Tooltip on top">Tooltip on top</button>
                                        </span>
                                        <span id="tooltip-container-bottom">
                                            <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                    data-bs-container="#tooltip-container-bottom"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Tooltip on bottom">Tooltip on bottom</button>
                                        </span>
                                        <span id="tooltip-container-left">
                                            <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                    data-bs-container="#tooltip-container-left" data-bs-toggle="tooltip"
                                                    data-bs-placement="left"
                                                    title="Tooltip on left">Tooltip on left</button>
                                        </span>
                                        <span id="tooltip-container-right">
                                            <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                    data-bs-container="#tooltip-container-right"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="Tooltip on right">Tooltip on right</button>
                                        </span>
                                    </div>
                                    <div class="code-block mt-4">
                                        <h6>Example</h6>
                                        <button class="btn btn-sm btn-copy-clipboard"
                                                data-clipboard-target="#tooltip-ex-1">Copy
                                        </button>

                                        <pre class="prettyprint lang-html escape" id="tooltip-ex-1">
                                            <button type="button" class="btn btn-soft-primary me-1 mb-2 mb-xl-0"
                                                    data-bs-container="#tooltip-container-top" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Tooltip on top">Tooltip on top</button>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tooltips End-->
                </div>
            </div>
        </div>
    </section>

@endsection

