<!-- resources/views/contact.blade.php -->
@extends('site::layouts.main')

@section('title', 'Contact Page')

@section('content')

@include('site::includes.navbar3')
        
<!-- Google Map -->
<div class="container-fluid relative mt-20">
    <div class="grid grid-cols-1">
        <div class="w-full leading-[0] border-0">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d39206.002432144705!2d-95.4973981212445!3d29.709510002925988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8640c16de81f3ca5%3A0xf43e0b60ae539ac9!2sGerald+D.+Hines+Waterwall+Park!5e0!3m2!1sen!2sin!4v1566305861440!5m2!1sen!2sin" style="border:0" class="w-full h-[500px]" allowfullscreen></iframe>
        </div>
    </div><!--end grid-->
</div><!--end container-->
<!-- Google Map -->

<!-- Start Section-->
<section class="relative lg:py-24 py-16">
    <div class="container">
        <div class="grid md:grid-cols-12 grid-cols-1 items-center gap-6">
            <div class="lg:col-span-7 md:col-span-6">
                <img src="{{ site_asset('images/travel-train-station.svg') }}" class="w-full max-w-[500px] mx-auto" alt="">
            </div>

            <div class="lg:col-span-5 md:col-span-6">
                <div class="lg:ms-5">
                    <div class="bg-white dark:bg-slate-900 rounded-md shadow-sm dark:shadow-gray-800 p-6">
                        <h3 class="mb-6 text-2xl leading-normal font-semibold">Get in touch !</h3>

                        <form method="post" action="#" name="myForm" id="myForm" onsubmit="return validateForm()">
                            <p class="mb-0" id="error-msg"></p>
                            <div id="simple-msg"></div>
                            <div class="grid lg:grid-cols-12 grid-cols-1 gap-3">
                                <div class="lg:col-span-6">
                                    <label for="name" class="font-semibold">Your Name:</label>
                                    <input name="name" id="name" type="text" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Name :">
                                </div>

                                <div class="lg:col-span-6">
                                    <label for="email" class="font-semibold">Your Email:</label>
                                    <input name="email" id="email" type="email" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Email :">
                                </div>

                                <div class="lg:col-span-12">
                                    <label for="subject" class="font-semibold">Your Question:</label>
                                    <input name="subject" id="subject" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Subject :">
                                </div>

                                <div class="lg:col-span-12">
                                    <label for="comments" class="font-semibold">Your Comment:</label>
                                    <textarea name="comments" id="comments" class="mt-2 w-full py-2 px-3 h-28 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Message :"></textarea>
                                </div>
                            </div>
                            <button type="submit" id="submit" name="send" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-2">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end container-->
    
    <div class="container lg:mt-24 mt-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-6">

            <!-- includes/Contact/contacts.blade.php -->
            @include('site::includes.Contact.contacts')

        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Section-->

@endsection