@php
$posts = [
    [
        'img' => 'assets/images/listing/1.jpg', 
    ],
    [
        'img' => 'assets/images/listing/2.jpg', 
    ],
    [
        'img' => 'assets/images/listing/3.jpg', 
    ],
    [
        'img' => 'assets/images/listing/4.jpg', 
    ],
    [
        'img' => 'assets/images/listing/5.jpg', 
    ],
    [
        'img' => 'assets/images/listing/6.jpg', 
    ],
    [
        'img' => 'assets/images/listing/7.jpg', 
    ],
    [
        'img' => 'assets/images/listing/8.jpg', 
    ],
    [
        'img' => 'assets/images/listing/9.jpg', 
    ],
    [
        'img' => 'assets/images/listing/10.jpg', 
    ],
    [
        'img' => 'assets/images/listing/11.jpg', 
    ],
    [
        'img' => 'assets/images/listing/12.jpg', 
    ],
    [
        'img' => 'assets/images/listing/5.jpg', 
    ],
    [
        'img' => 'assets/images/listing/8.jpg', 
    ],
    [
        'img' => 'assets/images/listing/4.jpg', 
    ]
];
@endphp

@foreach ($posts as $item)
    <div class="tiny-slide">
        <a href="{{ asset($item['img']) }}" class="lightbox d-inline-block" title="">
            <img src="{{ asset($item['img']) }}" class="sm:size-40 object-cover" alt="">
        </a>
    </div>
@endforeach