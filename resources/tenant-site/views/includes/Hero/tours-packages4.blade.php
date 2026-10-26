@php
$packages = [
    [
        'id' => 1,
        'img' => 'assets/images/listing/1.jpg', 
        'location' => 'Dubai',
        'title' => 'Cuba Sailing Adventure',
        'price-d' => '$ 58 / Day',
        'discount' => '10% Off',
    ],
    [
        'id' => 2,
        'img' => 'assets/images/listing/2.jpg', 
        'location' => 'Italy',
        'title' => 'Tour in New York',
        'price-d' => '$ 58 / Day',
        'discount' => 'false',
    ],
    [
        'id' => 3,
        'img' => 'assets/images/listing/3.jpg', 
        'location' => 'Maldivas',
        'title' => 'Discover Greece',
        'price-d' => '$ 58 / Day',
        'discount' => 'false',
    ],
    [
        'id' => 4,
        'img' => 'assets/images/listing/4.jpg', 
        'location' => 'USA',
        'title' => 'Museum of Modern Art',
        'price-d' => '$ 58 / Day',
        'discount' => 'false',
    ],
    [
        'id' => 5,
        'img' => 'assets/images/listing/5.jpg', 
        'location' => 'Bali',
        'title' => 'Peek Mountain View',
        'price-d' => '$ 58 / Day',
        'discount' => 'false',
    ],
    [
        'id' => 6,
        'img' => 'assets/images/listing/6.jpg', 
        'location' => 'Bangkok',
        'title' => 'Hot Baloon Journey',
        'price-d' => '$ 58 / Day',
        'discount' => '25% Off',
    ],
    [
        'id' => 7,
        'img' => 'assets/images/listing/7.jpg', 
        'location' => 'Singapore',
        'title' => 'Orca Camp Kayaking Trip',
        'price-d' => '$ 58 / Day',
        'discount' => 'false',
    ],
    [
        'id' => 8,
        'img' => 'assets/images/listing/8.jpg', 
        'location' => 'Thailand',
        'title' => 'Caño Cristales River Trip',
        'price-d' => '$ 58 / Day',
        'discount' => '20% Off',
    ]
];
@endphp

@foreach ($packages as $item)
    <div class="group rounded-md shadow-sm dark:shadow-gray-700">
        <div class="relative overflow-hidden rounded-md shadow-sm dark:shadow-gray-700 mx-2 mt-2">
            <img src="{{ asset($item['img']) }}" class="scale-125 group-hover:scale-100 duration-500" alt="">

            @if ($item['discount'] !== 'false')
                <div class="absolute top-0 start-0 p-4">
                    <span class="bg-red-500 text-white text-[12px] px-2.5 py-1 font-medium rounded-md h-5">{{ $item['discount'] }}</span>
                </div>
            @endif

            <div class="absolute top-0 end-0 p-4">
                <a href="javascript:void(0)" class="size-8 inline-flex justify-center items-center bg-white dark:bg-slate-900 shadow-sm dark:shadow-gray-800 rounded-full text-slate-100 dark:text-slate-700 focus:text-red-500 dark:focus:text-red-500 hover:text-red-500 dark:hover:text-red-500"><i class="mdi mdi-heart text-[20px] align-middle"></i></a>
            </div>
        </div>

        <div class="p-3">
            <p class="flex items-center text-slate-400 font-medium mb-2"><i data-feather="map-pin" class="text-red-500 size-4 me-1"></i> {{ $item['location'] }}</p>
            <a href="{{ url('/tour-detail-one') }}" class="text-lg font-medium hover:text-red-500 duration-500 ease-in-out">{{ $item['title'] }}</a>

            <div class="flex items-center mt-2">
                <span class="text-slate-400">Rating:</span>
                <ul class="text-lg font-medium text-amber-400 list-none ms-2">
                    <li class="inline"><i class="mdi mdi-star align-middle"></i></li>
                    <li class="inline"><i class="mdi mdi-star align-middle"></i></li>
                    <li class="inline"><i class="mdi mdi-star align-middle"></i></li>
                    <li class="inline"><i class="mdi mdi-star align-middle"></i></li>
                    <li class="inline"><i class="mdi mdi-star align-middle"></i></li>
                    <li class="inline text-slate-900 dark:text-white text-sm">5.0(30)</li>
                </ul>
            </div>
            
            <div class="mt-3 pt-3 flex justify-between items-center border-t border-slate-100 dark:border-gray-800">
                <h5 class="text-lg font-medium text-red-500">{{ $item['price-d'] }}</h5>

                <a href="" class="text-slate-400 hover:text-red-500">Explore Now <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
@endforeach