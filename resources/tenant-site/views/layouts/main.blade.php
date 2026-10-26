<!-- resources/views/layouts/main.blade.php -->

<!DOCTYPE html>
<html lang="en" class="light scroll-smooth" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <title>@yield('meta_title', $title ?? config('app.name'))</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="Tour & Travels Agency Tailwind CSS Template" name="description">
        <meta content="Tour, Travels, agency, business, corporate, tour packages, journey, trip, tailwind css, Admin, Landing" name="keywords">
        <meta name="author" content="Shreethemes">
        <meta name="website" content="https://shreethemes.in">
        <meta name="email" content="support@shreethemes.in">
        <meta name="version" content="1.5.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- favicon -->
        <link rel="shortcut icon" href="{{ site_asset('images/favicon.ico') }}">

        <!-- Css -->
        <link href="{{ site_asset('libs/swiper/css/swiper.min.css') }}" rel="stylesheet">
        <link href="{{ site_asset('libs/tobii/css/tobii.min.css') }}" rel="stylesheet">
        <link href="{{ site_asset('libs/tiny-slider/tiny-slider.css') }}" rel="stylesheet">
        <link href="{{ site_asset('libs/js-datepicker/datepicker.min.css') }}" rel="stylesheet">
        <!-- Main Css -->
        <link href="{{ site_asset('libs/@mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ site_asset('css/tailwind.css') }}" rel="stylesheet" type="text/css">

    </head>
    
    <body class="text-slate-900 dark:text-white bg-white dark:bg-slate-900">

        <!-- Main Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- includes/footer.blade.php -->
        @include('site::includes.footer')

        <!-- Switcher -->
        <div class="fixed top-1/4 -left-2 z-50">
            <span class="relative inline-block rotate-90">
                <input type="checkbox" class="checkbox opacity-0 absolute" id="chk">
                <label class="label bg-slate-900 dark:bg-white shadow-sm dark:shadow-gray-800 cursor-pointer rounded-full flex justify-between items-center p-1 w-14 h-8" for="chk">
                    <i data-feather="moon" class="w-[18px] h-[18px] text-yellow-500"></i>
                    <i data-feather="sun" class="w-[18px] h-[18px] text-yellow-500"></i>
                    <span class="ball bg-white dark:bg-slate-900 rounded-full absolute top-[2px] left-[2px] w-7 h-7"></span>
                </label>
            </span>
        </div>

        <!-- LTR & RTL Mode Code -->
        <div class="fixed top-[40%] -left-3 z-50">
            <a href="" id="switchRtl">
                <span class="py-1 px-3 relative inline-block rounded-b-md -rotate-90 bg-white dark:bg-slate-900 shadow-md dark:shadow-sm dark:shadow-gray-800 font-semibold rtl:block ltr:hidden" >LTR</span>
                <span class="py-1 px-3 relative inline-block rounded-b-md -rotate-90 bg-white dark:bg-slate-900 shadow-md dark:shadow-sm dark:shadow-gray-800 font-semibold ltr:block rtl:hidden">RTL</span>
            </a>
        </div>
        <!-- LTR & RTL Mode Code -->

        <!-- Back to top -->
        <a href="#" onclick="topFunction()" id="back-to-top" class="back-to-top fixed hidden text-lg rounded-md z-10 bottom-5 end-5 size-8 text-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white justify-center items-center"><i class="mdi mdi-arrow-up"></i></a>
        <!-- Back to top --> 

        <!-- JAVASCRIPTS -->
        <script src="{{ site_asset('libs/swiper/js/swiper.min.js') }}"></script>
        <script src="{{ site_asset('libs/gumshoejs/gumshoe.polyfills.min.js') }}"></script>
        <script src="{{ site_asset('libs/tobii/js/tobii.min.js') }}"></script>
        <script src="{{ site_asset('libs/tiny-slider/min/tiny-slider.js') }}"></script>
        <script src="{{ site_asset('libs/jarallax/jarallax.min.js') }}"></script>
        <script src="{{ site_asset('libs/js-datepicker/datepicker.min.js') }}"></script>
        <script src="{{ site_asset('js/easy_background.js') }}"></script>
        @stack('scripts')
        <script src="{{ site_asset('libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ site_asset('js/plugins.init.js') }}"></script>
        <script src="{{ site_asset('js/app.js') }}"></script>
        <!-- JAVASCRIPTS -->
    </body>
</html>