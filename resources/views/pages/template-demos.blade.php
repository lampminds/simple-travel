@extends('layouts.base', ['title' => 'Template Demos'])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true,'fixedWidth' => true, 'sticky' => true,'topbarColor' => 'navbar-light', 'classList' => 'ms-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Template Demos</h3>
                        <p class="mt-1 fw-medium">
                            Quick links to the original pages from the purchased template.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-3 g-3">
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Landings</h5>
                            <ul class="mb-0">
                                <li><a href="{{ route('second', ['landings', 'app']) }}">/landings/app</a></li>
                                <li><a href="{{ route('second', ['landings', 'saas']) }}">/landings/saas</a></li>
                                <li><a href="{{ route('second', ['landings', 'saas2']) }}">/landings/saas2</a></li>
                                <li><a href="{{ route('second', ['landings', 'startup']) }}">/landings/startup</a></li>
                                <li><a href="{{ route('second', ['landings', 'software']) }}">/landings/software</a></li>
                                <li><a href="{{ route('second', ['landings', 'agency']) }}">/landings/agency</a></li>
                                <li><a href="{{ route('second', ['landings', 'coworking']) }}">/landings/coworking</a></li>
                                <li><a href="{{ route('second', ['landings', 'crypto']) }}">/landings/crypto</a></li>
                                <li><a href="{{ route('second', ['landings', 'marketing']) }}">/landings/marketing</a></li>
                                <li><a href="{{ route('second', ['landings', 'portfolio']) }}">/landings/portfolio</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Pages</h5>
                            <ul class="mb-0">
                                <li><a href="{{ route('second', ['pages', 'company']) }}">/pages/company</a></li>
                                <li><a href="{{ route('second', ['pages', 'career']) }}">/pages/career</a></li>
                                <li><a href="{{ route('pages.pricing') }}">/pages/pricing</a></li>
                                <li><a href="{{ route('second', ['pages', 'help-desk']) }}">/pages/help-desk</a></li>
                                <li><a href="{{ route('second', ['pages', 'portfolio-grid']) }}">/pages/portfolio-grid</a></li>
                                <li><a href="{{ route('second', ['pages', 'portfolio-masonry']) }}">/pages/portfolio-masonry</a></li>
                                <li><a href="{{ route('second', ['pages', 'portfolio-item']) }}">/pages/portfolio-item</a></li>
                                <li><a href="{{ route('pages.digitalizar-operador-turistico') }}">/pages/digitalizar-operador-turistico</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">UI Kit</h5>
                            <ul class="mb-0">
                                <li><a href="{{ route('second', ['ui-kit', 'colors']) }}">/ui-kit/colors</a></li>
                                <li><a href="{{ route('second', ['ui-kit', 'typography']) }}">/ui-kit/typography</a></li>
                                <li><a href="{{ route('second', ['ui-kit', 'components']) }}">/ui-kit/components</a></li>
                                <li><a href="{{ route('second', ['ui-kit', 'custom']) }}">/ui-kit/custom</a></li>
                                <li><a href="{{ route('second', ['ui-kit', 'plugins']) }}">/ui-kit/plugins</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection

