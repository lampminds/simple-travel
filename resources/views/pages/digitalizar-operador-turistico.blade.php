@extends('layouts.base', ['title' => __('digitalizar.page_title')])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true, 'fixedWidth' => true, 'sticky' => true, 'topbarColor' => 'navbar-light', 'classList' => 'ms-auto', 'ctaButtonClass' => 'btn-outline-secondary btn-sm'])

    <section class="hero-4 pb-5 pt-7 py-sm-7 bg-gradient2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="hero-title">{{ __('digitalizar.hero_title') }}</h1>
                    <p class="fs-17 text-muted">{{ __('digitalizar.hero_lead') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section py-6 position-relative">
        <div class="container">
            <div class="row mb-3 pb-2 border-bottom align-items-end">
                <div class="col-lg-3 col-md-4 mb-0 d-none d-md-block">
                    <span class="text-muted small text-uppercase fw-semibold">{{ __('digitalizar.topic') }}</span>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h2 class="h4 text-muted text-center mb-0">{{ __('digitalizar.column_sin') }}</h2>
                </div>
                <div class="col-lg-5 col-md-4">
                    <h2 class="h4 text-primary text-center mb-0">{{ __('digitalizar.column_con') }}</h2>
                </div>
            </div>

            @foreach ($rows as $index => $row)
                <div class="row align-items-stretch border-bottom py-4" data-aos="fade-up" data-aos-duration="{{ 200 + ($index + 1) * 100 }}">
                    <div class="col-lg-3 col-md-4 mb-3 mb-md-0 pe-lg-3">
                        <h3 class="h5 mb-0">{{ __($row['title_key']) }}</h3>
                    </div>
                    <div class="col-lg-4 col-md-4 pe-lg-3 overflow-hidden">
                        @if ($row['img_wo'])
                            <img src="{{ asset($row['img_wo']) }}" alt="" class="float-start me-3 mb-2 rounded" style="max-width: 140px; height: auto;" loading="lazy">
                        @endif
                        <p class="text-muted mb-0">{{ __($row['sin_key']) }}</p>
                    </div>
                    <div class="col-lg-5 col-md-4 mt-3 mt-md-0 overflow-hidden">
                        @if ($row['img_w'])
                            <img src="{{ asset($row['img_w']) }}" alt="" class="float-start me-3 mb-2 rounded" style="max-width: 140px; height: auto;" loading="lazy">
                        @endif
                        <p class="mb-0">{{ __($row['con_key']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section pt-4 pb-6">
        <div class="container text-center">
            <a href="{{ route('pages.pricing') }}" class="btn btn-primary">{{ __('saas.view_plans') }}</a>
        </div>
    </section>

    <x-site-footer />

@endsection
