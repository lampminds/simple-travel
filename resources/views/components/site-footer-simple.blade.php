@props([])

<!-- footer start -->
<section class="section py-4 position-relative" {{ $attributes }}>
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <ul class="list-inline list-with-separator mb-0">
                    @auth
                        @if(auth()->user()->hasRoleForCurrentAccount('owner'))
                            <li class="list-inline-item me-0"><a href="{{ route('account.tasks.index') }}">{{ __('account.tasks_nav') }}</a></li>
                        @endif
                    @endauth
                    <li class="list-inline-item me-0"><a href="{{ route('pages.about') }}">About</a></li>
                    <li class="list-inline-item me-0"><a href="{{ route('pages.privacy') }}">Privacy</a></li>
                    <li class="list-inline-item me-0"><a href="{{ route('pages.terms') }}">Terms</a></li>
                    <li class="list-inline-item me-0"><span class="text-muted">FAQ</span></li>
                    <li class="list-inline-item me-0"><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="col-md-auto text-md-end mt-2 mt-md-0">
                <p class="fs-14 mb-0">
                    <script>document.write(new Date().getFullYear())</script>
                    © Prompt. All rights reserved. Developed by
                    <a href="https://lampminds.com/">Lampminds</a>
                </p>
            </div>
        </div>
    </div>
</section>
<!-- footer end -->
