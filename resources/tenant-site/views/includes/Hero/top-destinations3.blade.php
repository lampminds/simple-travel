@php
$tops = [
    [
        'img' => 'assets/images/listing/1.jpg', 
        'name' => 'Rome, Italy',
        'title' => '3 Hotels'
    ],
    [
        'img' => 'assets/images/listing/2.jpg', 
        'name' => 'Singapore',
        'title' => '3 Hotels'
    ],
    [
        'img' => 'assets/images/listing/3.jpg', 
        'name' => 'Paris, France',
        'title' => '3 Hotels'
    ],
    [
        'img' => 'assets/images/listing/4.jpg', 
        'name' => 'Goa, India',
        'title' => '3 Hotels'
    ],
    [
        'img' => 'assets/images/listing/5.jpg', 
        'name' => 'Whistler, Canada',
        'title' => '3 Hotels'
    ],
    [
        'img' => 'assets/images/listing/6.jpg', 
        'name' => ' Malaysia',
        'title' => '3 Hotels'
    ],
    [
        'img' => 'assets/images/listing/7.jpg', 
        'name' => 'Sydney, Australia',
        'title' => '3 Hotels'
    ]
];
@endphp

@foreach ($tops as $item)
    <div class="tiny-slide">
        <div class="group text-center m-2">
            <div class="relative overflow-hidden rounded-full">
                <img src="{{ asset($item['img']) }}" class="w-full md:h-32 h-36 object-cover scale-125 group-hover:scale-100 duration-500 shadow-sm dark:shadow-gray-800" alt="">
            </div>

            <div class="mt-2">
                <a href="" class="text-lg font-medium hover:text-red-500 duration-500 ease-in-out">{{ $item['name'] }}</a>
                <p class="text-slate-400 text-sm duration-500">{{ $item['title'] }}</p>
            </div>
        </div>
    </div>
@endforeach