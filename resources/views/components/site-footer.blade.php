@props([
    'logoHeight' => 60,
])

<!-- footer start -->
<section class="pt-5 pb-4 bg-gradient3 position-relative" {{ $attributes }}>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <x-site-logo :height="$logoHeight" :url="route('home')" class="navbar-brand me-lg-4 mb-4 me-auto d-flex align-items-center pt-0"/>
                <p class="text-muted w-75">
                    {{ __('footer.tagline') }}
                </p>
            </div>
            <div class="col-md-auto col-sm-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">{{ __('footer.platform') }}</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="{{ route('pages.demo') }}" class="text-muted">{{ __('footer.demo') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.pricing') }}" class="text-muted">{{ __('footer.pricing') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.integrations') }}" class="text-muted">{{ __('footer.integrations') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-auto col-sm-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">{{ __('footer.help') }}</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="{{ route('pages.help-center') }}" class="text-muted">{{ __('footer.help_center') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.api') }}" class="text-muted">{{ __('footer.api') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-auto col-sm-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">{{ __('footer.company') }}</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="{{ route('pages.about') }}" class="text-muted">{{ __('footer.about') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.faq') }}" class="text-muted">{{ __('footer.faq') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.contact') }}" class="text-muted">{{ __('footer.contact') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-auto col-6">
                <div class="ps-md-5">
                    <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">{{ __('footer.legal') }}</h6>
                    <ul class="list-unstyled">
                        <li class="my-3"><a href="{{ route('pages.usage-policy') }}" class="text-muted">{{ __('footer.usage_policy') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.privacy') }}" class="text-muted">{{ __('footer.privacy') }}</a></li>
                        <li class="my-3"><a href="{{ route('pages.terms') }}" class="text-muted">{{ __('footer.terms') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row text-md-start text-center">
            <div class="col-md-6">
                <p class="pb-0 mb-0 text-muted">
                    <script>document.write(new Date().getFullYear())</script>
                    {{ __('footer.copyright') }}
                    {{ __('footer.developed_by') }}
                    <a href="https://lampminds.com/">Lampminds</a>
                </p>
            </div>
        </div>
    </div>
</section>
<!-- footer end -->
