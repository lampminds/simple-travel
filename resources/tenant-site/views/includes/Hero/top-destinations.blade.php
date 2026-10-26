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
        'name' => 'Kuala Lumpur, Malaysia',
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
        <div class="group relative overflow-hidden rounded-md shadow-sm dark:shadow-gray-800 m-2">
            <img src="{{ asset($item['img']) }}" class="w-full h-72 object-cover scale-125 group-hover:scale-100 duration-500" alt="">
            <div class="absolute inset-0 bg-gradient-to-b to-slate-900 from-transparent opacity-0 group-hover:opacity-100 duration-500"></div>
            <div class="absolute p-4 bottom-0 start-0">
                <a href="" class="text-lg font-medium text-white hover:text-red-500 duration-500 ease-in-out">{{ $item['name'] }}</a>
                <p class="text-white/70 group-hover:text-white text-sm duration-500">{{ $item['title'] }}</p>
            </div>
        </div>
    </div>
@endforeach