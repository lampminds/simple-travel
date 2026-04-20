@extends('layouts.base', ['title' => __('account.tasks_page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.tasks_heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted">{{ __('account.tasks_intro') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($categories->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.tasks_empty') }}
                            </div>
                        </div>
                    @else
                        @foreach ($categories as $category)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">{{ $category->displayLabel() }}</h5>
                                    @if (filled($category->description))
                                        <p class="mb-0 mt-2 small text-muted">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($category->tasks as $task)
                                        <li class="list-group-item py-3">
                                            <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-md-between gap-3">
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-inline-flex align-items-center gap-2 flex-wrap">
                                                        <span class="fw-semibold">{{ $task->displayLabel() }}</span>
                                                        @if ($url = $task->welcomeExecutionUrl())
                                                            <a
                                                                href="{{ $url }}"
                                                                class="btn btn-link btn-sm p-0 text-primary d-inline-flex align-items-center"
                                                                title="{{ __('account.tasks_open_step') }}"
                                                            >
                                                                <span class="visually-hidden">{{ __('account.tasks_open_step') }}</span>
                                                                <span class="icon-xs text-primary">
                                                                    @svg('/duotone-icons/navigation/Arrow-right')
                                                                </span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    @if (filled($task->description))
                                                        <p class="mb-0 mt-1 small text-muted">{{ $task->description }}</p>
                                                    @endif
                                                </div>
                                                @if ($task->opensActionInBrowser() && filled($task->action_url))
                                                    <div class="flex-shrink-0">
                                                        <a href="{{ $task->action_url }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener noreferrer">
                                                            {{ __('account.tasks_open_link') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
