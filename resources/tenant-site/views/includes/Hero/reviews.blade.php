@php
$reviews = [
    [
        'img' => 'assets/images/client/01.jpg', 
        'name' => 'Calvin Carlo',
        'title' => 'Manager',
        'desc' => "It seems that only fragments of the original text remain in the Lorem Ipsum texts used today."
    ],
    [
        'img' => 'assets/images/client/02.jpg', 
        'name' => 'Christa Smith',
        'title' => 'Manager',
        'desc' => "The most well-known dummy text is the 'Lorem Ipsum', which is said to have originated in the 16th century"
    ],
    [
        'img' => 'assets/images/client/03.jpg', 
        'name' => 'Jemina CLone',
        'title' => 'Manager',
        'desc' => "One disadvantage of Lorum Ipsum is that in Latin certain letters appear more frequently than others."
    ],
    [
        'img' => 'assets/images/client/04.jpg', 
        'name' => 'Smith Vodka',
        'title' => 'Manager',
        'desc' => "Thus, Lorem Ipsum has only limited suitability as a visual filler for German texts."
    ],
    [
        'img' => 'assets/images/client/05.jpg', 
        'name' => 'Cristino Murfi',
        'title' => 'Manager',
        'desc' => "There is now an abundance of readable dummy texts. These are usually used when a text is required."
    ],
    [
        'img' => 'assets/images/client/06.jpg', 
        'name' => 'Cristino Murf',
        'title' => 'Manager',
        'desc' => "According to most sources, Lorum Ipsum can be traced back to a text composed by Cicero."
    ]
];
@endphp

@foreach ($reviews as $item)
    <div class="tiny-slide text-center">
        <div class="cursor-e-resize">
            <div class="content relative rounded shadow-sm dark:shadow-gray-700 m-2 p-6 bg-white dark:bg-slate-900 before:content-[''] before:absolute before:start-1/2 before:-bottom-[4px] before:box-border before:border-8 before:rotate-[45deg] before:border-t-transparent before:border-e-white dark:before:border-e-slate-900 before:border-b-white dark:before:border-b-slate-900 before:border-s-transparent before:shadow-testi dark:before:shadow-gray-700 before:origin-top-left">
                <i class="mdi mdi-format-quote-open mdi-48px text-red-500"></i>
                <p class="text-slate-400">" {{ $item['desc'] }} "</p>
                <ul class="list-none mb-0 text-amber-400 mt-3">
                    <li class="inline"><i class="mdi mdi-star"></i></li>
                    <li class="inline"><i class="mdi mdi-star"></i></li>
                    <li class="inline"><i class="mdi mdi-star"></i></li>
                    <li class="inline"><i class="mdi mdi-star"></i></li>
                    <li class="inline"><i class="mdi mdi-star"></i></li>
                </ul>
            </div>
            
            <div class="text-center mt-5">
                <img src="{{ asset($item['img']) }}" class="size-14 rounded-full shadow-md dark:shadow-gray-700 mx-auto" alt="">
                <h6 class="mt-2 font-semibold">{{ $item['name'] }}</h6>
                <span class="text-slate-400 text-sm">{{ $item['title'] }}</span>
            </div>
        </div>
    </div>
@endforeach