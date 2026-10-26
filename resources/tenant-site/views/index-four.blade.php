<!-- resources/views/index-four.blade.php -->
@extends('site::layouts.main')

@section('title', 'Index-Four Page')

@section('content')

@include('site::includes.navbar')
        
<!-- Hero Start -->
<section class="relative py-36 bg-cover jarallax" data-jarallax data-speed="0.5" style="background-image: url('{{ site_asset('images/bg/6.jpg') }}');">
    <div class="absolute inset-0 bg-slate-900/40"></div>
    <div class="container relative">
        <div class="grid lg:grid-cols-12 md:grid-cols-2 mt-10 items-center gap-6">
            <div class="lg:col-span-7">
                <h5 class="text-3xl !font-dancing text-white">Find Your Ideal Stay</h5>
                <h4 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">Where Do You <br> Want To Go?</h4>
                <p class="text-white/70 text-xl max-w-xl">Planning for a trip? We will organize your trip with the best places and within best budget!</p>

                <div class="mt-6">
                    <a href="" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">View Packages</a>
                    <a href="#!" data-type="youtube" data-id="S_CGed6E610" class="size-10 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center rounded-full border border-red-500 bg-red-500 text-white ms-1 lightbox"><i data-feather="video" class="h-4 w-4"></i></a><span class="font-semibold ms-1 align-middle"></span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-gray-800 p-6 z-10 relative lg:ms-10">
                    <h4 class="mb-5 text-2xl font-semibold">Search Your Destinations</h4>

                    <form>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="form-label font-medium text-slate-900 dark:text-white">Search:</label>
                                <div class="relative mt-2">
                                    <i data-feather="search" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                    <input name="name" type="text" id="job-keyword" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Search">
                                </div>
                            </div>

                            <div>
                                <label class="form-label font-medium text-slate-900 dark:text-white">Select Your Date:</label>
                                <div class="relative mt-2">
                                    <i data-feather="calendar" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                    <input name="name" type="text" id="job-keyword" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 start" placeholder="Select Your Date">
                                </div>
                            </div>

                            <div>
                                <label class="form-label font-medium text-slate-900 dark:text-white">Select Your Date:</label>
                                <div class="relative mt-2">
                                    <i data-feather="calendar" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                    <input name="name" type="text" id="job-keyword" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 end" placeholder="Select Your Date">
                                </div>
                            </div>

                            <div>
                                <label class="form-label font-medium text-slate-900 dark:text-white">No. of Person:</label>
                                <div class="relative mt-2">
                                    <i data-feather="users" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                    <select class="form-select w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                        <option disabled selected>No. of person</option>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                            </div>

                            <div class="">
                                <input type="submit" id="search-buy" name="search" class="py-1 px-5 h-10 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full cursor-pointer" value="Search">
                            </div>
                        </div><!--end grid-->
                    </form>
                    <!-- end form -->
                </div>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<div class="relative">
    <div class="absolute block w-full h-auto bottom-[25px] z-1 start-0">
        <a href="#about"><i class="mdi mdi-arrow-down absolute top-0 start-0 end-0 text-center inline-flex items-center justify-center rounded-full bg-white dark:bg-slate-900 h-12 w-12 mx-auto shadow-md dark:shadow-gray-800"></i></a>
    </div>

    <div class="shape absolute sm:-bottom-px -bottom-[2px] start-0 end-0 overflow-hidden text-white dark:text-slate-900">
        <svg class="w-full h-auto scale-[2.0] origin-top" viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
        </svg>
    </div>
</div>
<!-- Hero End -->

<!-- Start -->
<section class="relative md:py-24 py-16">
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Top Destinations</h3>

            <p class="text-slate-400 max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
        </div><!--end grid-->

        <div class="grid grid-cols-1 relative mt-4">
            <div class="tiny-six-item">

                <!-- includes/Hero/top-destinations3.blade.php -->
                @include('site::includes.Hero.top-destinations3')

            </div><!--end slider-->
        </div><!--end grid-->
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-8 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Tours Packages</h3>

            <p class="text-slate-400 max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
        </div><!--end grid-->

        <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 mt-6 gap-6">

            <!-- includes/Hero/tours-packages4.blade.php -->
            @include('site::includes.Hero.tours-packages4')

        </div><!--end grid-->

        <div class="mt-6 text-center">
            <a href="{{ url('/grid-right-sidebar') }}" class="text-slate-400 hover:text-red-500 inline-block">See More Tours <i class="mdi mdi-arrow-right align-middle"></i></a>
        </div>
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-6 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">What Our Users Say</h3>

            <p class="text-slate-400 max-w-xl mx-auto">This is just a simple text made for this unique and awesome template, you can replace it with any text.</p>
        </div><!--end grid-->

        <div class="grid grid-cols-1 mt-6">
            <div class="tiny-three-item">
                
                <!-- includes/Hero/reviews.blade.php -->
                @include('site::includes.Hero.reviews')

            </div>
        </div><!--end grid-->
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-6 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Frequently Asked Questions</h3>

            <p class="text-slate-400 max-w-xl mx-auto">This is just a simple text made for this unique and awesome template, you can replace it with any text.</p>
        </div><!--end grid-->

        <div class="relative grid md:grid-cols-12 grid-cols-1 items-center mt-6 gap-6">

            <!-- includes/Hero/asked-questions.blade.php -->
            @include('site::includes.Hero.asked-questions')

        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

@endsection