<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/head', ['title' => $title])
    @yield('css')
    @vite('node_modules/aos/dist/aos.css')
    @vite('node_modules/swiper/swiper-bundle.min.css')
    @vite('node_modules/magnific-popup/dist/magnific-popup.css')
    @vite('node_modules/bootstrap-select/dist/css/bootstrap-select.min.css')
    @vite('node_modules/leaflet/dist/leaflet.css')
    @vite('node_modules/jarallax/dist/jarallax.css')
    @vite('resources/scss/theme.scss')
</head>

<body>

@yield('content')

<!-- back to top -->
<a class="btn btn-soft-primary shadow-none btn-icon btn-back-to-top" href='#'><i class="icon-xxs"
                                                                                 data-feather="arrow-up"></i></a>

@yield('script')
@vite(['resources/js/theme.js','resources/js/swiper.js'])
@yield('script-bottom')
</body>

</html>
