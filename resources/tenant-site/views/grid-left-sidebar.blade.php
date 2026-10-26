<!-- resources/views/grid-left-sidebar.blade.php -->
@extends('site::layouts.main')

@section('title', 'Grid-Left-Sidebar Page')

@section('content')

@include('site::includes.navbar4')
        
<!-- Start Hero -->
<section class="relative table w-full items-center py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('{{ site_asset('images/bg/cta.jpg') }}');">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center mt-10">
            <h3 class="text-4xl leading-normal tracking-wider font-semibold text-white">Tour Packages</h3>
        </div><!--end grid-->
    </div><!--end container-->
    
    <div class="absolute text-center z-10 bottom-5 start-0 end-0 mx-3">
        <ul class="tracking-[0.5px] mb-0 inline-block">
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white/50 hover:text-white"><a href="{{ url('/') }}">Site</a></li>
            <li class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"><i class="mdi mdi-chevron-right"></i></li>
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white" aria-current="page">Tour</li>
        </ul>
    </div>
</section><!--end section-->
<!-- End Hero -->

<!-- Start -->
<section class="relative md:py-24 py-16">
    <div class="container relative">
        <div class="grid md:grid-cols-12 grid-cols-1 gap-6">
            <div class="lg:col-span-4 md:col-span-5">
                <div class="p-4 rounded-md shadow-sm dark:shadow-gray-700 sticky top-20">
                    <div>
                        <h5 class="text-lg font-medium">Price Filter</h5>

                        <div class="range-slider mt-3">
                            <span class="flex justify-between pb-2">
                                <span>
                                    <label for="" class="text-lg">$</label>
                                    <input type="number" class="text-slate-400" value="50" min="0" max="200"/>
                                </span>

                                <span>
                                    <label for="" class="text-lg">$</label>
                                    <input type="number" class="text-slate-400" value="120" min="0" max="200"/>
                                </span>
                            </span>
                            <input value="50" min="0" max="200" step="5" type="range"/>
                            <input value="120" min="0" max="200" step="5" type="range"/>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h5 class="text-lg font-medium">Reviews</h5>

                        <div class="mt-3">
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="5star">
                                <label class="form-checkbox-label text-slate-400" for="5star">
                                    <ul class="font-medium list-none">
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                    </ul>
                                </label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="4star">
                                <label class="form-checkbox-label text-slate-400" for="4star">
                                    <ul class="font-medium list-none">
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                    </ul>
                                </label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="3star">
                                <label class="form-checkbox-label text-slate-400" for="3star">
                                    <ul class="font-medium list-none">
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                    </ul>
                                </label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="2star">
                                <label class="form-checkbox-label text-slate-400" for="2star">
                                    <ul class="font-medium list-none">
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                    </ul>
                                </label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="1star">
                                <label class="form-checkbox-label text-slate-400" for="1star">
                                    <ul class="font-medium list-none">
                                        <li class="inline"><i class="mdi mdi-star text-amber-400 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                        <li class="inline"><i class="mdi mdi-star-outline text-slate-200 dark:text-gray-700 align-middle"></i></li>
                                    </ul>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h5 class="text-lg font-medium">Booking Activity</h5>

                        <div class="mt-3">
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="Bali">
                                <label class="form-checkbox-label text-slate-400" for="Bali">Bali Park</label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="Paris">
                                <label class="form-checkbox-label text-slate-400" for="Paris">Paris</label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="Arcadia">
                                <label class="form-checkbox-label text-slate-400" for="Arcadia">Arcadia</label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="Culver">
                                <label class="form-checkbox-label text-slate-400" for="Culver">Culver City</label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="Long">
                                <label class="form-checkbox-label text-slate-400" for="Long">Long Beach</label>
                            </div>
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="California">
                                <label class="form-checkbox-label text-slate-400" for="California">California</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h5 class="text-lg font-medium">Tour Map</h5>

                        <div class="mt-3">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d39206.002432144705!2d-95.4973981212445!3d29.709510002925988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8640c16de81f3ca5%3A0xf43e0b60ae539ac9!2sGerald+D.+Hines+Waterwall+Park!5e0!3m2!1sen!2sin!4v1566305861440!5m2!1sen!2sin" style="border:0" class="w-full h-[300px] rounded-full" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 md:col-span-7">
                <div class="grid lg:grid-cols-2 grid-cols-1 gap-6">
                    
                    <!-- includes/Listing/packages.blade.php -->
                    @include('site::includes.Listing.packages')

                </div><!--end grid-->

                <div class="grid md:grid-cols-12 grid-cols-1 mt-6">
                    <div class="md:col-span-12 text-center">
                        
                        <!-- includes/Listing/navigation.blade.php -->
                        @include('site::includes.Listing.navigation')

                    </div><!--end col-->
                </div><!--end grid-->
            </div>
        </div>
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

@endsection