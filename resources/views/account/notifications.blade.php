@extends('layouts.base', ['title' => __('account.notifications.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.notifications.heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ __('account.notifications.intro', ['account' => $account->commercial_name ?? $account->name ?? $account->nick]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if (session('status'))
                        <div class="alert alert-success mb-3" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($notifications->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.notifications.empty') }}
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <ul class="list-group list-group-flush">
                                @foreach ($notifications as $notification)
                                    <li class="list-group-item py-3">
                                        <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-md-between gap-3">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                    <span class="fw-semibold">{{ $notification->title }}</span>
                                                    <span class="badge {{ $notification->read_at ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }}">
                                                        {{ $notification->read_at ? __('account.notifications.read') : __('account.notifications.unread') }}
                                                    </span>
                                                    <span class="badge bg-light text-body border">{{ $notification->type }}</span>
                                                    @if ($notification->recipientUser)
                                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                                            {{ __('account.notifications.recipient_private', ['name' => $notification->recipientUser->name]) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                            {{ __('account.notifications.recipient_broadcast') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                                <div class="small text-muted">
                                                    @if ($notification->authorName())
                                                        {{ __('account.notifications.by_author', ['name' => $notification->authorName()]) }}
                                                    @endif
                                                    {{ __('account.notifications.created_at_human', ['time' => $notification->created_at?->diffForHumans()]) }}
                                                    @if ($notification->read_at)
                                                        {{ __('account.notifications.read_at_human', ['time' => $notification->read_at->diffForHumans()]) }}
                                                        @if ($notification->readByUser)
                                                            {{ __('account.notifications.read_by', ['name' => $notification->readByUser->name]) }}
                                                        @else
                                                            .
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            @if (! $notification->read_at)
                                                <div class="flex-shrink-0">
                                                    <form method="POST" action="{{ route('account.notifications.read', $notification) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.notifications.mark_as_read') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('account.notifications.create_heading') }}</h5>
                            <form method="POST" action="{{ route('account.notifications.store') }}" class="row g-3">
                                @csrf
                                <div class="col-md-4">
                                    <label for="recipient_user_id" class="form-label">{{ __('account.notifications.recipient_label') }}</label>
                                    <select id="recipient_user_id" name="recipient_user_id" class="form-select">
                                        <option value="">{{ __('account.notifications.recipient_all') }}</option>
                                        @foreach($recipientUsers as $recipientUser)
                                            <option value="{{ $recipientUser->id }}" @selected((string) old('recipient_user_id') === (string) $recipientUser->id)>
                                                {{ $recipientUser->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="title" class="form-label">{{ __('account.notifications.title_label') }}</label>
                                    <input
                                        id="title"
                                        name="title"
                                        type="text"
                                        class="form-control"
                                        maxlength="180"
                                        required
                                        value="{{ old('title') }}"
                                    >
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">{{ __('account.notifications.message_label') }}</label>
                                    <textarea
                                        id="message"
                                        name="message"
                                        class="form-control"
                                        rows="3"
                                        maxlength="4000"
                                        required
                                    >{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('account.notifications.send_button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
