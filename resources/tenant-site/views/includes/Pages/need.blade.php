@php
$needs = [
    [
        'icon' => 'help-circle', 
        'title' => 'FAQs',
        'desc' => 'The phrasal sequence of the is now so that many campaign and benefit'
    ],
    [
        'icon' => 'bookmark', 
        'title' => 'Guides / Support',
        'desc' => 'The phrasal sequence of the is now so that many campaign and benefit'
    ],
    [
        'icon' => 'settings', 
        'title' => 'Support Request',
        'desc' => 'The phrasal sequence of the is now so that many campaign and benefit'
    ]
];
@endphp

@foreach ($needs as $item)
    <div class="text-center px-6 mt-6">
        <div class="w-20 h-20 bg-red-500/5 text-red-500 rounded-xl text-3xl flex align-middle justify-center items-center shadow-xs dark:shadow-gray-800 mx-auto">
            <i data-feather="{{ $item['icon'] }}" class="size-[30px]"></i>
        </div>

        <div class="content mt-7">
            <a href="{{ url('/helpcenter-faqs') }}" class="h5 text-lg font-medium hover:text-red-500">{{ $item['title'] }}</a>
            <p class="text-slate-400 mt-3">{{ $item['desc'] }}</p>
            
            <div class="mt-5">
                <a href="{{ url('/helpcenter-faqs') }}" class="text-red-500">Read More <i class="mdi mdi-chevron-right"></i></a>
            </div>
        </div>
    </div>
@endforeach