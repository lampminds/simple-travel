@props([
    'tagline' => 'Make your web application stand out with high-quality landing page',
    'logoHeight' => 60,
])

<!-- footer start -->
<section class="pt-5 pb-4 bg-gradient3 position-relative" {{ $attributes }}>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <x-site-logo :height="$logoHeight" :url="route('home')" class="navbar-brand me-lg-4 mb-4 me-auto d-flex align-items-center pt-0"/>
                <p class="text-muted w-75">
                    {{ $tagline }}
                </p>
            </div>
            <div class="col-md-auto col-sm-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">Platform</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="#" class="text-muted">Demo</a></li>
                        <li class="my-3"><a href="#" class="text-muted">Pricing</a></li>
                        <li class="my-3"><a href="#" class="text-muted">Integrations</a></li>
                        <li class="my-3"><a href="#" class="text-muted">Status</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-auto col-sm-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">Knowledge Base</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="#" class="text-muted">Blog</a></li>
                        <li class="my-3"><a href="#" class="text-muted">Help Center</a></li>
                        <li class="my-3"><a href="#" class="text-muted">Sales Tools catalog</a></li>
                        <li class="my-3"><a href="#" class="text-muted">API</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-auto col-sm-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">Company</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="{{ route('pages.about') }}" class="text-muted">About Us</a></li>
                        <li class="my-3"><span class="text-muted">FAQ</span></li>
                        <li class="my-3"><a href="#" class="text-muted">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-auto col-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="#" class="text-muted">Usage Policy</a></li>
                        <li class="my-3"><a href="{{ route('pages.privacy') }}" class="text-muted">Privacy Policy</a></li>
                        <li class="my-3"><a href="{{ route('pages.terms') }}" class="text-muted">Terms of Service</a></li>
                        <li class="my-3"><a href="#" class="text-muted">Trust</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row text-md-start text-center">
            <div class="col-md-6">
                <p class="pb-0 mb-0 text-muted">
                    <script>document.write(new Date().getFullYear())</script>
                    © Prompt. All rights reserved. Crafted
                    by <a href="https://coderthemes.com/">Coderthemes</a>
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="align-items-end mt-md-0 mt-4">
                    <ul class="list-unstyled mb-0">
                        <li class="d-inline-block me-4">
                            <a href=""><i data-feather="facebook" class="icon icon-xs"></i></a>
                        </li>
                        <li class="d-inline-block me-4">
                            <a href=""><i data-feather="twitter" class="icon icon-xs"></i></a>
                        </li>
                        <li class="d-inline-block">
                            <a href=""><i data-feather="linkedin" class="icon icon-xs"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- footer end -->
