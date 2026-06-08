@props([
    /** @var list<array{id: int, question: string, answer: string}> $items */
    'items' => [],
    'accordionId' => 'faqContent',
])

@if (count($items) > 0)
    <div id="{{ $accordionId }}">
        <div class="accordion custom-accordionwitharrow" id="accordion-{{ $accordionId }}">
            @foreach ($items as $index => $item)
                @php
                    $collapseId = 'faq-collapse-'.$item['id'];
                    $headingId = 'faq-heading-'.$item['id'];
                    $isFirst = $index === 0;
                @endphp
                <div @class(['card mb-1 border rounded-sm', 'mb-0' => $loop->last])>
                    <a href=""
                       @class(['text-dark', 'collapsed' => ! $isFirst])
                       data-bs-toggle="collapse"
                       data-bs-target="#{{ $collapseId }}"
                       aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                       aria-controls="{{ $collapseId }}">
                        <div class="card-header" id="{{ $headingId }}">
                            <h5 class="my-1 fw-medium">{{ $item['question'] }}
                                <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                            </h5>
                        </div>
                    </a>
                    <div id="{{ $collapseId }}"
                         @class(['collapse', 'show' => $isFirst])
                         aria-labelledby="{{ $headingId }}"
                         data-bs-parent="#accordion-{{ $accordionId }}">
                        <div class="card-body text-muted pt-1">
                            {!! nl2br(e($item['answer'])) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
