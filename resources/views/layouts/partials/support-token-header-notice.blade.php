@if(auth()->check() && session()->get(\App\Support\WebsiteImpersonationSession::KEY_ACTIVE_VIA_SUPPORT_TOKEN))
    <div class="border-bottom border-danger" style="background-color: rgba(220, 53, 69, 0.12);">
        <div class="container py-2">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-2 small text-body">
                <span>
                    <span class="fw-semibold text-danger-emphasis">{{ __('impersonation.header_badge') }}</span>
                    <span class="text-danger-emphasis"> — {{ __('impersonation.header_body', ['name' => auth()->user()->name]) }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        {{ __('impersonation.header_logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
