@php
$blogs = [
    [
        'id' => 1,
        'img' => 'assets/images/blog/1.jpg', 
        'name' => 'Travel',
        'title' => 'This Spanish city is a feast for the eyes: Site',
        'desc' => "This is required when, for example, the final text is not yet available."
    ],
    [
        'id' => 2,
        'img' => 'assets/images/blog/2.jpg', 
        'name' => 'Tour',
        'title' => 'New Zealand’s South Island brims with majestic',
        'desc' => "This is required when, for example, the final text is not yet available."
    ],
    [
        'id' => 3,
        'img' => 'assets/images/blog/3.jpg', 
        'name' => 'Tourist',
        'title' => 'When you visit the Eternal Rome City: Site',
        'desc' => "This is required when, for example, the final text is not yet available."
    ]
];
@endphp

@foreach ($blogs as $item)
    <div class="group relative overflow-hidden">
        <div class="relative overflow-hidden rounded-md shadow-sm dark:shadow-gray-800">
            <img src="{{ asset($item['img']) }}" class="group-hover:scale-110 group-hover:rotate-3 duration-500" alt="">
            <div class="absolute top-0 start-0 p-4 opacity-0 group-hover:opacity-100 duration-500">
                <span class="bg-red-500 text-white text-[12px] px-2.5 py-1 font-medium rounded-md h-5">{{ $item['name'] }}</span>
            </div>
        </div>

        <div class="mt-6">
            <div class="flex mb-4">
                <span class="flex items-center text-slate-400 text-sm"><i data-feather="clock" class="size-4 text-slate-900 dark:text-white me-1.5"></i>5 min read</span>
                <span class="text-slate-400 text-sm ms-3">by <a href="" class="text-slate-900 dark:text-white hover:text-red-500 dark:hover:text-red-500 font-medium">Site</a></span>
            </div>

            <a href="{{ url('/blog-detail') }}" class="text-lg font-medium hover:text-red-500 duration-500 ease-in-out">{{ $item['title'] }}</a>
            <p class="text-slate-400 mt-2">{{ $item['desc'] }}</p>

            <div class="mt-3">
                <a href="{{ url('/blog-detail') }}" class="hover:text-red-500 inline-flex items-center">Read More <i data-feather="chevron-right" class="size-4 ms-1"></i></a>
            </div>
        </div>
    </div>
@endforeach