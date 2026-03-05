@extends('layouts.base', ['title' => __('pricing.page_title')])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true, 'fixedWidth' => true, 'sticky' => true, 'topbarColor' => 'navbar-light', 'classList' => 'ms-auto', 'ctaButtonClass' => 'btn-outline-secondary btn-sm'])

    <section class="hero-4 pb-5 pt-7 py-sm-7 bg-gradient2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <h1 class="hero-title">{{ __('pricing.hero_title') }}</h1>
                    <p class="fs-17 text-muted">{{ __('pricing.hero_lead') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Block 1: Abono base por rango de usuarios -->
    <section class="section py-6 position-relative">
        <div class="container">
            @php $defaultRangeLabel = !empty($rangeTabs) ? (collect($rangeTabs)->firstWhere('up_to', $defaultUpTo)['label'] ?? '') : ''; @endphp
            <div class="row mb-3">
                <div class="col text-center">
                    <h2 class="display-6 fw-semibold">{{ __('pricing.block1_heading') }}</h2>
                    <p class="text-muted mb-0 mt-2">{{ __('pricing.block1_intro') }}</p>
                </div>
            </div>
            @if(!empty($rangeTabs))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 justify-content-center overflow-auto pb-2" data-range-tabs data-selected-up-to="{{ $defaultUpTo }}">
                            @foreach($rangeTabs as $tab)
                                <button type="button" class="btn {{ $tab['up_to'] == $defaultUpTo ? 'btn-primary' : 'btn-outline-primary' }} px-4 py-2 rounded-pill flex-shrink-0" data-up-to="{{ $tab['up_to'] }}" data-label="{{ e($tab['label']) }}" aria-pressed="{{ $tab['up_to'] == $defaultUpTo ? 'true' : 'false' }}">
                                    <span class="d-block">{{ $tab['label'] }}</span>
                                    @if($tab['base_price'] !== null && $tab['base_price'] !== '')
                                        <span class="small opacity-90">{{ __('pricing.currency') }}{{ $tab['base_price'] }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <p class="text-center mb-4">
                <span class="fw-semibold text-primary py-2 px-3 rounded bg-soft-primary d-inline-block">
                    {{ __('pricing.block1_highlight') }}
                </span>
            </p>
            @if($starterPlan ?? null)
                <div class="row">
                    <div class="col-12">
                        @if($starterPlan->name)
                            <h4 class="text-primary mb-3">{{ $starterPlan->name }}</h4>
                        @endif
                        @php
                                $firstOpenIndex = null;
                                foreach ($starterPlan->items as $idx => $it) {
                                    if ($it->display_text && $it->children->filter(fn ($c) => $c->display_text !== null && $c->display_text !== '')->isNotEmpty()) {
                                        $firstOpenIndex = $idx;
                                        break;
                                    }
                                }
                            @endphp
                        <div class="accordion accordion-flush" id="starter-accordion">
                            @foreach($starterPlan->items as $index => $item)
                                @if($item->display_text)
                                    @php
                                        $collapseId = 'starter-collapse-' . $index;
                                        $headingId = 'starter-heading-' . $index;
                                        $hasChildren = $item->children->filter(fn ($c) => $c->display_text !== null && $c->display_text !== '')->isNotEmpty();
                                        $isFirstOpen = $hasChildren && $index === $firstOpenIndex;
                                    @endphp
                                    <div class="accordion-item border rounded-sm mb-2">
                                        <h5 class="accordion-header" id="{{ $headingId }}">
                                            <button class="accordion-button {{ $hasChildren && !$isFirstOpen ? 'collapsed' : '' }} {{ !$hasChildren ? 'disabled' : '' }} py-3 {{ !$hasChildren ? 'pe-3' : '' }}" type="button" data-bs-toggle="{{ $hasChildren ? 'collapse' : '' }}" data-bs-target="{{ $hasChildren ? '#' . $collapseId : '' }}" aria-expanded="{{ $isFirstOpen ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                                <span class="fw-bold">{{ $item->display_text }}</span>
                                                @if($hasChildren)
                                                    <i class="icon-xs accordion-arrow ms-2" data-feather="chevron-down"></i>
                                                @endif
                                            </button>
                                        </h5>
                                        @if($hasChildren)
                                            <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $isFirstOpen ? 'show' : '' }}" aria-labelledby="{{ $headingId }}" data-bs-parent="#starter-accordion">
                                                <div class="accordion-body pt-0 pb-3">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($item->children as $child)
                                                            @if($child->display_text)
                                                                <li class="py-2 d-flex align-items-center">
                                                                    <i class="align-middle icon-dual-success me-2 icon-xs flex-shrink-0" data-feather="check"></i>
                                                                    <span>{{ $child->display_text }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Block 2: Módulos adicionales -->
    <section class="section py-6 position-relative bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col text-center">
                    <h2 class="display-6 fw-semibold">{{ __('pricing.block2_heading') }}</h2>
                    <p class="text-muted mx-auto mt-3 mb-0" style="max-width: 42rem;">
                        {{ __('pricing.block2_intro') }}
                    </p>
                </div>
            </div>
            <div class="row align-items-start mt-5">
                @forelse($modulePlans ?? [] as $index => $plan)
                    @php
                        $pricesForPlan = $modulePricesByRange[$plan->id] ?? [];
                        $displayPrice = (string) ($pricesForPlan[$defaultUpTo ?? ''] ?? $plan->price ?? '');
                    @endphp
                    <div class="col-lg-4 col-xl-4 mb-4">
                        <div class="card border hoverable h-100" data-aos="fade-up" data-aos-duration="{{ 300 + ($index * 200) }}">
                            <div class="card-body text-center d-flex flex-column">
                                @if($plan->name)
                                    <h4 class="my-0 text-primary">{{ $plan->name }}</h4>
                                @endif
                                @if($plan->price !== null || $displayPrice !== '')
                                    <h2 class="mb-0 mt-2">
                                        <span class="fw-normal text-muted fs-13 align-top">{{ __('pricing.currency') }}</span>
                                        <span class="fw-bolder display-5 module-price-display" data-prices="{{ json_encode($pricesForPlan) }}">{{ $displayPrice }}</span>
                                        <span class="fw-normal text-muted fs-13 align-middle"> {{ __('pricing.per_month') }}</span>
                                    </h2>
                                    @if(($defaultRangeLabel ?? '') !== '')
                                        <p class="small text-muted mb-0 mt-1"><span class="module-range-label">{{ $defaultRangeLabel }}</span></p>
                                    @endif
                                @endif
                                @if($plan->description)
                                    <p class="text-muted small mt-2 mb-0">{{ $plan->description }}</p>
                                @endif
                                <ul class="list-unstyled border-top py-4 mt-4 text-start flex-grow-1">
                                    @foreach($plan->items as $item)
                                        @if($item->display_text)
                                            <li class="py-2 d-flex align-items-center">
                                                <span class="fw-bold">{{ $item->display_text }}</span>
                                            </li>
                                            @foreach($item->children as $child)
                                                @if($child->display_text)
                                                    <li class="py-2 d-flex align-items-center ps-3 border-start border-2 border-light">
                                                        <i class="align-middle icon-dual-success me-2 icon-xs" data-feather="check"></i>
                                                        <span>{{ $child->display_text }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </ul>
                                <a href="#" class="btn btn-soft-primary d-block mt-auto">{{ __('pricing.sign_up_now') }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">
                        <p class="mb-0">{{ __('pricing.no_plans') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>


    <!-- benefits start -->
    <section class="pt-5 pb-7 career-service position-relative">
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
                        <span
                            class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-sm text-primary me-4 flex-shrink-0">
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
                        <span
                            class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-md text-primary me-4 flex-shrink-0">
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
                        <span
                            class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-sm text-primary me-4 flex-shrink-0">
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
                        <span
                            class="bg-soft-primary avatar avatar-md rounded icon icon-with-bg icon-sm text-primary me-4 flex-shrink-0">
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

    <!-- faq start -->
    <section class="section py-6 pt-sm-6 pb-sm-7 position-relative bg-light">
        <div class="container" data-aos="fade-up" data-aos-duration="600">
            <div class="row">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('pricing.faq_badge') }}</span>
                    <h1 class="display-5 fw-semibold">{{ __('pricing.faq_heading') }}</h1>
                    <p class="text-muted mx-auto">
                        {{ __('pricing.faq_subtitle') }}
                    </p>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-10 col-lg-8">
                    <div id="faqContent">
                        <div class="accordion custom-accordionwitharrow" id="accordionExample">
                            <div class="card mb-1 border rounded-sm">
                                <a href="" class="text-dark" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                   aria-expanded="true" aria-controls="collapseOne">
                                    <div class="card-header" id="headingOne">
                                        <h5 class="my-1 fw-medium">{{ __('pricing.faq1_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('pricing.faq1_a') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-1 border rounded-sm">
                                <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                   data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <div class="card-header" id="headingTwo">
                                        <h5 class="my-1 fw-medium">{{ __('pricing.faq2_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('pricing.faq2_a') }}
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0 border rounded-sm">
                                <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                   data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <div class="card-header" id="headingThree">
                                        <h5 class="my-1 fw-medium">{{ __('pricing.faq3_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('pricing.faq3_a') }}
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0 border rounded-sm">
                                <a href="" class="text-dark collapsed" data-bs-toggle="collapse"
                                   data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="my-1 fw-medium">{{ __('pricing.faq4_q') }}
                                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                                        </h5>
                                    </div>
                                </a>
                                <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                     data-bs-parent="#accordionExample">
                                    <div class="card-body text-muted pt-1">
                                        {{ __('pricing.faq4_a') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- faq end -->

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
                            <a href="{{ route('second', ['pages', 'help-desk']) }}"
                               class="btn btn-outline-primary mt-4">{{ __('pricing.cta_kb_button') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- cta end -->

    <!-- footer start -->
    <section class="mt-lg-5 pt-5 pb-4 bg-gradient3 position-relative">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <x-site-logo class="navbar-brand me-lg-4 mb-4 me-auto d-flex align-items-center pt-0" :url="'#'" />
                    <p class="text-muted w-75">
                        {{ __('pricing.footer_tagline') }}
                    </p>
                </div>
                <div class="col-md-auto col-sm-6">
                    <div class="ps-md-5">
                        <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">
                            {{ __('pricing.footer_platform') }}</h6>
                        <ul class="list-unstyled">
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_demo') }}</a></li>
                            <li class="my-3"><a href="{{ route('second', ['pages', 'pricing']) }}" class="text-muted">{{ __('pricing.footer_pricing') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_integrations') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_status') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-auto col-sm-6">
                    <div class="ps-md-5">
                        <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">
                            {{ __('pricing.footer_kb') }}</h6>
                        <ul class="list-unstyled">
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_blog') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_help_center') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">API</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-auto col-sm-6">
                    <div class="ps-md-5">
                        <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">
                            {{ __('pricing.footer_company') }}</h6>
                        <ul class="list-unstyled">
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_about') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_careers') }}</a></li>
                            <li class="my-3"><a href="{{ route('second', ['pages', 'contact']) }}" class="text-muted">{{ __('pricing.footer_contact') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-auto col-6">
                    <div class="ps-md-5">
                        <h6 class="mb-4 mt-5 mt-sm-2 fs-14 fw-semibold text-uppercase">
                            {{ __('pricing.footer_legal') }}
                        </h6>
                        <ul class="list-unstyled">
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_usage_policy') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_privacy') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_terms') }}</a></li>
                            <li class="my-3"><a href="#" class="text-muted">{{ __('pricing.footer_trust') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row text-md-start text-center">
                <div class="col-md-6">
                    <p class="pb-0 mb-0 text-muted">
                        <script>document.write(new Date().getFullYear())</script>
                        {{ __('pricing.footer_copyright') }}
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
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.querySelector('[data-range-tabs]');
    if (!container) return;

    function switchRange(btn) {
        var upTo = btn.getAttribute('data-up-to');
        var label = btn.getAttribute('data-label') || '';
        if (!upTo) return;

        container.setAttribute('data-selected-up-to', upTo);

        container.querySelectorAll('button[data-up-to]').forEach(function(b) {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
            b.setAttribute('aria-pressed', 'false');
        });
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
        btn.setAttribute('aria-pressed', 'true');

        document.querySelectorAll('.module-price-display').forEach(function(el) {
            var raw = el.getAttribute('data-prices');
            if (!raw) return;
            try {
                var prices = JSON.parse(raw);
                el.textContent = prices[upTo] !== undefined && prices[upTo] !== null ? prices[upTo] : el.textContent;
            } catch (e) {}
        });

        document.querySelectorAll('.module-range-label').forEach(function(el) {
            el.textContent = label;
        });
    }

    container.querySelectorAll('button[data-up-to]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            switchRange(this);
        });
    });
});
</script>
@endsection


