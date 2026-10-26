<!-- resources/views/index-two.blade.php -->
@extends('site::layouts.main')

@section('title', 'Index-Two Page')

@section('content')

@include('site::includes.navbar')
        
<!-- Start Hero -->
<section class="swiper-slider-hero relative block h-screen" id="home">
    <div class="swiper-container absolute end-0 top-0 w-full h-full">
        <div class="swiper-wrapper">
            <div class="swiper-slide flex items-center overflow-hidden">
                <div class="slide-inner absolute end-0 top-0 w-full h-full slide-bg-image flex items-center bg-center;" data-background="{{ site_asset('images/bg/2.jpg') }}">
                    <div class="absolute inset-0 bg-slate-900/70"></div>
                    <div class="container relative">
                        <div class="grid grid-cols-1">
                            <div class="text-center">
                                <img src="{{ site_asset('images/map-plane.png') }}" class="mx-auto w-[300px]" alt="">
                                <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">Find Your Best <br> Travels Package</h1>
                                <p class="text-white/70 text-xl max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
                                
                                <div class="mt-6">
                                    <a href="" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">See More</a>
                                </div>
                            </div>
                        </div><!--end grid-->
                    </div><!--end container-->
                </div><!-- end slide-inner --> 
            </div> <!-- end swiper-slide -->

            <div class="swiper-slide flex items-center overflow-hidden">
                <div class="slide-inner absolute end-0 top-0 w-full h-full slide-bg-image flex items-center bg-center;" data-background="{{ site_asset('images/bg/3.jpg') }}">
                    <div class="absolute inset-0 bg-slate-900/70"></div>
                    <div class="container relative">
                        <div class="grid grid-cols-1">
                            <div class="text-center">
                                <img src="{{ site_asset('images/map-plane.png') }}" class="mx-auto w-[300px]" alt="">
                                <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">Find Next Best <br> Place To Visit</h1>
                                <p class="text-white/70 text-xl max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
                                
                                <div class="mt-6">
                                    <a href="" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">See More</a>
                                </div>
                            </div>
                        </div><!--end grid-->
                    </div><!--end container-->
                </div><!-- end slide-inner --> 
            </div> <!-- end swiper-slide -->
        </div>
        <!-- end swiper-wrapper -->

        <!-- swipper controls -->
        <div class="swiper-pagination"></div>
        <!-- <div class="swiper-button-next bg-transparent w-[35px] h-[35px] leading-[35px] -mt-[30px] bg-none border border-solid border-white/50 text-white hover:bg-red-500 hover:border-red-500 rounded-full text-center"></div>
        <div class="swiper-button-prev bg-transparent w-[35px] h-[35px] leading-[35px] -mt-[30px] bg-none border border-solid border-white/50 text-white hover:bg-red-500 hover:border-red-500 rounded-full text-center"></div> -->
    </div><!--end container-->
</section><!--end section-->
<!-- Hero End -->

<!-- Start -->
<section class="relative py-16 bg-gray-50 dark:bg-slate-800">
    <div class="container relative">
        <div class="grid grid-cols-1">
            <form class="p-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-gray-700">
                <div class="registration-form text-slate-900 text-start">
                    <div class="grid lg:grid-cols-5 md:grid-cols-2 grid-cols-1 gap-4">
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
                            <label class="form-label font-medium text-slate-900 dark:text-white">No. of person:</label>
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

                        <div class="lg:mt-[35px]">
                            <input type="submit" id="search-buy" name="search" class="py-1 px-5 h-10 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full cursor-pointer" value="Search">
                        </div>
                    </div><!--end grid-->
                </div><!--end container-->
            </form><!--end form-->
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

<!-- Start -->
<section class="relative md:py-24 py-16 overflow-hidden">
    <div class="container relative">
        
        <!-- includes/Hero/about.blade.php -->
        @include('site::includes.Hero.about')

    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-8 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Tours Packages</h3>

            <p class="text-slate-400 max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
        </div><!--end grid-->

        <div class="grid lg:grid-cols-2 grid-cols-1 mt-6 gap-6">                    
            
            <!-- includes/Hero/tours-packages2.blade.php -->
            @include('site::includes.Hero.tours-packages2')

        </div><!--end grid-->
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
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Travel Blogs</h3>

            <p class="text-slate-400 max-w-xl mx-auto">This is just a simple text made for this unique and awesome template, you can replace it with any text.</p>
        </div><!--end grid-->

        <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 mt-6 gap-6">
            
            <!-- includes/Hero/blog.blade.php -->
            @include('site::includes.Hero.blog')

        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

@endsection