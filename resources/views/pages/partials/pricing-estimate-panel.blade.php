@php
    $suffix = $panelContext === 'mobile' ? '-mobile' : '';
@endphp

<div class="card border shadow-sm pricing-estimate-panel {{ $panelContext === 'desktop' ? 'sticky-top' : '' }}" @if($panelContext === 'desktop') style="top: 10.5rem;" @endif>
    <div class="card-body d-flex flex-column">
        @if($panelContext === 'desktop')
            <h3 class="h5 fw-semibold mb-1">{{ __('pricing.estimate_heading') }}</h3>
            <p class="text-muted small mb-3">{{ __('pricing.estimate_intro') }}</p>
        @endif

        <div class="text-muted small py-4 text-center d-none" data-breakdown-empty{{ $suffix }}>
            {{ __('pricing.estimate_empty') }}
        </div>

        <div class="d-none flex-grow-1 d-flex flex-column" data-breakdown-content{{ $suffix }}>
            <div class="pricing-estimate-lines overflow-auto flex-grow-1" style="max-height: 18rem;" data-breakdown-lines{{ $suffix }}></div>
            <div class="pt-3 mt-2 border-top">
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <span class="fw-bold">{{ __('pricing.estimate_total') }}</span>
                    <span class="fs-4 fw-bold text-primary text-nowrap" data-breakdown-total{{ $suffix }}></span>
                </div>
                <p class="small text-muted mb-0 mt-1" data-breakdown-context{{ $suffix }}></p>
            </div>
            <a
                href="{{ route('second', ['pages', 'contact']) }}"
                class="btn btn-primary w-100 mt-3"
            >
                {{ __('pricing.estimate_cta') }}
            </a>
        </div>
    </div>
</div>
