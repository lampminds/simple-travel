@php
$contacts = [
    [
        'icon' => 'phone', 
        'name' => 'Phone',
        'title' => 'The phrasal sequence of the is now so that many campaign and benefit',
        'info' => '+152 534-468-854',
        'link' => 'tel:+152534-468-854',
        'type' => '',
        'style' => '',
    ],
    [
        'icon' => 'mail', 
        'name' => 'Email',
        'title' => 'The phrasal sequence of the is now so that many campaign and benefit',
        'info' => 'contact@example.com',
        'link' => 'mailto:contact@example.com',
        'type' => '',
        'style' => '',
    ],
    [
        'icon' => 'map-pin', 
        'name' => 'Location',
        'title' => 'C/54 Northwest Freeway, Suite 558, Houston, USA 485',
        'info' => 'View on Google map',
        'link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d39206.002432144705!2d-95.4973981212445!3d29.709510002925988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8640c16de81f3ca5%3A0xf43e0b60ae539ac9!2sGerald+D.+Hines+Waterwall+Park!5e0!3m2!1sen!2sin!4v1566305861440!5m2!1sen!2sin',
        'type' => 'iframe',
        'style' => 'video-play-icon read-more lightbox',
    ]
];
@endphp

@foreach ($contacts as $item)
    <div class="text-center px-6">
        <div class="relative text-transparent">
            <div class="size-20 bg-red-500/5 text-red-500 rounded-xl text-2xl flex align-middle justify-center items-center mx-auto shadow-xs dark:shadow-gray-800">
                <i data-feather="{{ $item['icon'] }}"></i>
            </div>
        </div>

        <div class="content mt-7">
            <h5 class="h5 text-lg font-semibold">{{ $item['name'] }}</h5>
            <p class="text-slate-400 mt-3">{{ $item['title'] }}</p>
            
            <div class="mt-5">
                <a href="{{ $item['link'] }}"
                data-type="{{ $item['type'] }}" class="{{ $item['style'] }} text-red-500 font-medium">{{ $item['info'] }}</a>
            </div>
        </div>
    </div>
@endforeach