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
            @foreach ($rows as $index => $row)
                <article
                    class="comparison-block py-5 {{ ! $loop->last ? 'border-bottom' : '' }}"
                    data-aos="fade-up"
                    data-aos-duration="{{ 200 + ($index + 1) * 100 }}"
                >
                    <h2 class="h2 text-center mb-4">{{ __($row['title_key']) }}</h2>

                    @if ($row['img'])
                        <div class="text-center mb-4 mb-lg-5">
                            <img
                                src="{{ asset($row['img']) }}"
                                alt="{{ __($row['title_key']) }}"
                                class="img-fluid rounded"
                                loading="lazy"
                            >
                        </div>
                    @endif

                    <div class="row g-4 g-lg-5 justify-content-center">
                        <div class="col-lg-6">
                            <h3 class="h4 text-muted text-center mb-3">{{ __('digitalizar.column_sin') }}</h3>
                            <p class="text-muted mb-0 text-center">{{ __($row['sin_key']) }}</p>
                        </div>
                        <div class="col-lg-6">
                            <h3 class="h4 text-primary text-center mb-3">{{ __('digitalizar.column_con') }}</h3>
                            <p class="mb-0 text-center">{{ __($row['con_key']) }}</p>
                        </div>
                    </div>
                </article>
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
