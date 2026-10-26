<!-- resources/views/tour-detail-one.blade.php -->
@extends('site::layouts.main')

@section('title', 'Tour-Detail-One Page')

@section('content')

@include('site::includes.navbar2')

<!-- Start Hero -->
<section class="relative table w-full items-center py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('{{ site_asset('images/bg/cta.jpg') }}');">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center mt-10">
            <h3 class="text-3xl leading-normal tracking-wider font-semibold text-white">
                @if(!empty($item['title']))
                    {{ $item['title'] }}
                @else
                    New Zealand’s South Island brims with majestic
                @endif
            </h3>
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
            <div class="lg:col-span-8 md:col-span-7">
                <div class="grid grid-cols-12 gap-4">
                    <div class="md:col-span-8 col-span-7">
                        <div class="group relative overflow-hidden rounded shadow-sm dark:shadow-gray-800">
                            <img src="{{ !empty($item['img']) ? asset($item['img']) : site_asset('images/listing/1.jpg') }}" class="w-full lg:h-60 md:h-44 h-48 object-cover" alt="">
                            <div class="absolute inset-0 group-hover:bg-slate-900/70 duration-500 ease-in-out"></div>
                            <div class="absolute top-1/2 -translate-y-1/2 start-0 end-0 text-center opacity-0 group-hover:opacity-100 duration-500">
                                <a href="{{ !empty($item['img']) ? asset($item['img']) : site_asset('images/listing/1.jpg') }}" class="inline-flex justify-center items-center size-9 bg-red-500 text-white rounded-full lightbox"><i data-feather="camera" class="size-4 align-middle"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 col-span-5">
                        <div class="group relative overflow-hidden rounded shadow-sm dark:shadow-gray-800">
                            <img src="{{ site_asset('images/listing/2.jpg') }}" class="w-full lg:h-60 md:h-44 h-48 object-cover" alt="">
                            <div class="absolute inset-0 group-hover:bg-slate-900/70 duration-500 ease-in-out"></div>
                            <div class="absolute top-1/2 -translate-y-1/2 start-0 end-0 text-center opacity-0 group-hover:opacity-100 duration-500">
                                <a href="{{ site_asset('images/listing/2.jpg') }}" class="inline-flex justify-center items-center size-9 bg-red-500 text-white rounded-full lightbox"><i data-feather="camera" class="size-4 align-middle"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 col-span-5">
                        <div class="group relative overflow-hidden rounded shadow-sm dark:shadow-gray-800">
                            <img src="{{ site_asset('images/listing/3.jpg') }}" class="w-full lg:h-60 md:h-44 h-48 object-cover" alt="">
                            <div class="absolute inset-0 group-hover:bg-slate-900/70 duration-500 ease-in-out"></div>
                            <div class="absolute top-1/2 -translate-y-1/2 start-0 end-0 text-center opacity-0 group-hover:opacity-100 duration-500">
                                <a href="{{ site_asset('images/listing/3.jpg') }}" class="inline-flex justify-center items-center size-9 bg-red-500 text-white rounded-full lightbox"><i data-feather="camera" class="size-4 align-middle"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-8 col-span-7">
                        <div class="group relative overflow-hidden rounded shadow-sm dark:shadow-gray-800">
                            <img src="{{ site_asset('images/listing/4.jpg') }}" class="w-full lg:h-60 md:h-44 h-48 object-cover" alt="">
                            <div class="absolute inset-0 group-hover:bg-slate-900/70 duration-500 ease-in-out"></div>
                            <div class="absolute top-1/2 -translate-y-1/2 start-0 end-0 text-center opacity-0 group-hover:opacity-100 duration-500">
                                <a href="{{ site_asset('images/listing/4.jpg') }}" class="inline-flex justify-center items-center size-9 bg-red-500 text-white rounded-full lightbox"><i data-feather="camera" class="size-4 align-middle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="text-2xl font-semibold mt-5">New Zealand’s South Island brims with majestic</h5>
                <p class="flex items-center text-slate-400 font-medium mt-2"><i data-feather="map-pin" class="size-4 me-1"></i> New Zealand</p>

                <ul class="list-none">
                    <li class="inline-flex items-center me-5 mt-5">
                        <i data-feather="clock" class="size-6 stroke-[1.5] text-red-500"></i>

                        <div class="ms-3">
                            <p class="font-medium">Duration</p>
                            <span class="text-slate-400 font-medium text-sm">2 day</span>
                        </div>
                    </li>

                    <li class="inline-flex items-center me-5 mt-5">
                        <i data-feather="activity" class="size-6 stroke-[1.5] text-red-500"></i>

                        <div class="ms-3">
                            <p class="font-medium">Type</p>
                            <span class="text-slate-400 font-medium text-sm">Adventure</span>
                        </div>
                    </li>

                    <li class="inline-flex items-center me-5 mt-5">
                        <i data-feather="users" class="size-6 stroke-[1.5] text-red-500"></i>

                        <div class="ms-3">
                            <p class="font-medium">Group Size:</p>
                            <span class="text-slate-400 font-medium text-sm">50 Peoples</span>
                        </div>
                    </li>

                    <li class="inline-flex items-center me-5 mt-5">
                        <i data-feather="globe" class="size-6 stroke-[1.5] text-red-500"></i>

                        <div class="ms-3">
                            <p class="font-medium">Languages</p>
                            <span class="text-slate-400 font-medium text-sm">English</span>
                        </div>
                    </li>

                    <li class="inline-flex items-center me-5 mt-5">
                        <i data-feather="dollar-sign" class="size-6 stroke-[1.5] text-red-500"></i>

                        <div class="ms-3">
                            <p class="font-medium">$50 / Person</p>
                            <span class="text-slate-400 font-medium text-sm">1 Day</span>
                        </div>
                    </li>
                </ul>

                <div class="mt-6">
                    <h5 class="text-lg font-semibold">Tour Descriptions:</h5>

                    <p class="text-slate-400 mt-6">The most well-known dummy text is the 'Lorem Ipsum', which is said to have originated in the 16th century. Lorem Ipsum is composed in a pseudo-Latin language which more or less corresponds to 'proper' Latin. It contains a series of real Latin words. This ancient dummy text is also incomprehensible, but it imitates the rhythm of most European languages in Latin script.</p>
                    <p class="text-slate-400 mt-3">The advantage of its Latin origin and the relative meaninglessness of Lorum Ipsum is that the text does not attract attention to itself or distract the viewer's attention from the layout.</p>
                </div>

                <div class="mt-6">
                    <h5 class="text-lg font-semibold">Questions & Answers:</h5>

                    <div id="accordion-collapseone" data-accordion="collapse" class="mt-6">
                        <div class="relative shadow-sm dark:shadow-gray-800 rounded-md overflow-hidden">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-1">
                                <button type="button" class="flex justify-between items-center p-5 w-full font-medium text-start" data-accordion-target="#accordion-collapse-body-1" aria-expanded="true" aria-controls="accordion-collapse-body-1">
                                    <span>How does it work ?</span>
                                    <svg data-accordion-icon class="size-4 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-1" class="hidden" aria-labelledby="accordion-collapse-heading-1">
                                <div class="p-5">
                                    <p class="text-slate-400 dark:text-gray-400">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative shadow-sm dark:shadow-gray-800 rounded-md overflow-hidden mt-4">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-2">
                                <button type="button" class="flex justify-between items-center p-5 w-full font-medium text-start" data-accordion-target="#accordion-collapse-body-2" aria-expanded="false" aria-controls="accordion-collapse-body-2">
                                    <span>Do I need a designer to use Site ?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-2" class="hidden" aria-labelledby="accordion-collapse-heading-2">
                                <div class="p-5">
                                    <p class="text-slate-400 dark:text-gray-400">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative shadow-sm dark:shadow-gray-800 rounded-md overflow-hidden mt-4">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-3">
                                <button type="button" class="flex justify-between items-center p-5 w-full font-medium text-start" data-accordion-target="#accordion-collapse-body-3" aria-expanded="false" aria-controls="accordion-collapse-body-3">
                                    <span>What do I need to do to start selling ?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-3" class="hidden" aria-labelledby="accordion-collapse-heading-3">
                                <div class="p-5">
                                    <p class="text-slate-400 dark:text-gray-400">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative shadow-sm dark:shadow-gray-800 rounded-md overflow-hidden mt-4">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-4">
                                <button type="button" class="flex justify-between items-center p-5 w-full font-medium text-start" data-accordion-target="#accordion-collapse-body-4" aria-expanded="false" aria-controls="accordion-collapse-body-4">
                                    <span>What happens when I receive an order ?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-4" class="hidden" aria-labelledby="accordion-collapse-heading-4">
                                <div class="p-5">
                                    <p class="text-slate-400 dark:text-gray-400">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h5 class="text-lg font-semibold">Leave A Comment:</h5>

                    <form class="mt-6">
                        <div class="grid lg:grid-cols-12 lg:gap-6">
                            <div class="lg:col-span-6 mb-5">
                                <div class="text-left">
                                    <label for="name" class="font-semibold">Your Name:</label>
                                    <input name="name" id="name" type="text" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Name :">
                                </div>
                            </div>

                            <div class="lg:col-span-6 mb-5">
                                <div class="text-left">
                                    <label for="email" class="font-semibold">Your Email:</label>
                                    <input name="email" id="email" type="email" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Email :">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1">
                            <div class="mb-5">
                                <div class="text-left">
                                    <label for="comments" class="font-semibold">Your Comment:</label>
                                    <textarea name="comments" id="comments" class="mt-3 w-full py-2 px-3 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 h-28" placeholder="Message :"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="submit" name="send" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-4 md:col-span-5">
                <div class="p-4 rounded-md shadow-sm dark:shadow-gray-700 sticky top-20">
                    <div>
                        <label class="font-semibold">Date:</label>
                        <input name="date" type="text" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 start" placeholder="Select date :">
                    </div>

                    <div class="mt-4">
                        <div class="md:flex">
                            <div class="md:w-1/3">
                                <span class="font-medium">Adult:</span>
                            </div>

                            <div class="md:w-2/3 mt-4 md:mt-0">
                                <form>
                                    <div class="form-icon relative">
                                        <i data-feather="user" class="w-4 h-4 absolute top-3 start-4"></i>
                                        <input type="number" class="w-full ps-12 py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="No. of person" id="Noperson" name="number" required="">
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="md:flex mt-4">
                            <div class="md:w-1/3">
                                <span class="font-medium">Child:</span>
                            </div>

                            <div class="md:w-2/3 mt-4 md:mt-0">
                                <form>
                                    <div class="form-icon relative">
                                        <i data-feather="users" class="w-4 h-4 absolute top-3 start-4"></i>
                                        <input type="number" class="w-full ps-12 py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="No. of children" id="Nochildren" name="number" required="">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">Search Now</button>
                    </div>

                    <div class="mt-6">
                        <h5 class="text-lg font-medium">Tour Map</h5>

                        <div class="mt-3">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d39206.002432144705!2d-95.4973981212445!3d29.709510002925988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8640c16de81f3ca5%3A0xf43e0b60ae539ac9!2sGerald+D.+Hines+Waterwall+Park!5e0!3m2!1sen!2sin!4v1566305861440!5m2!1sen!2sin" style="border:0" class="w-full h-[300px] rounded-full" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

@endsection