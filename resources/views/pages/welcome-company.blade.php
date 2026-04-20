@extends('layouts.base', ['title' => __('welcome_company.page_title')])

@php
    $welcomeRobot = asset('images/robots/welcome2-sm.png');
    $welcomeUserName = auth()->user()?->name ?: auth()->user()?->email;
    $welcomeNameLine1 = null;
    $welcomeNameLine2 = null;
    if (is_string($welcomeUserName) && $welcomeUserName !== '') {
        $tokens = preg_split('/\s+/u', trim($welcomeUserName), -1, PREG_SPLIT_NO_EMPTY);
        if (count($tokens) >= 2) {
            $welcomeNameLine2 = array_pop($tokens);
            $welcomeNameLine1 = implode(' ', $tokens);
        } elseif (count($tokens) === 1) {
            $welcomeNameLine1 = $tokens[0];
        }
    }
@endphp

@section('css')
    <style>
        /**
         * Name overlay on the blank sign (coordinates are % of the image box).
         * Defaults match welcome2-sm.png (robot + white sign). Adjust if you swap the asset.
         */
        .welcome-company-hero {
            --welcome-sign-x: 70%;
            --welcome-sign-y: 21%;
            --welcome-sign-max-width: 30%;
            --welcome-sign-font-size: clamp(0.65rem, 1.85vw, 1.05rem);
            --welcome-sign-rotate: 0deg;
            --welcome-sign-color: #212529;
        }

        .welcome-company-hero__wrap {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }

        .welcome-company-hero__wrap img {
            display: block;
        }

        .welcome-company-hero__name {
            position: absolute;
            left: var(--welcome-sign-x);
            top: var(--welcome-sign-y);
            transform: translate(-50%, -50%) rotate(var(--welcome-sign-rotate));
            max-width: var(--welcome-sign-max-width);
            font-size: var(--welcome-sign-font-size);
            font-weight: 600;
            line-height: 1.12;
            text-align: center;
            color: var(--welcome-sign-color);
            pointer-events: none;
            word-break: break-word;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.08em;
        }

        .welcome-company-hero__name-line {
            display: block;
        }
    </style>
@endsection

@section('content')

    @include('layouts.partials.navbar', [
        'hideSearch' => true,
        'fixedWidth' => true,
        'sticky' => false,
        'topbarColor' => 'navbar-light',
        'classList' => 'ms-auto',
    ])

    <section class="position-relative py-5 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row align-items-center gy-5">
                <div class="col-lg-6 order-2 order-lg-1">
                    <h1 class="display-6 fw-semibold mb-3">{{ __('welcome_company.headline') }}</h1>
                    <p class="text-muted fs-16 mb-4">{{ __('welcome_company.intro') }}</p>

                    <h2 class="h5 fw-semibold mb-3">{{ __('welcome_company.todo_heading') }}</h2>
                    @if(($todoTasks ?? collect())->isEmpty())
                        <p class="text-muted mb-4">{{ __('welcome_company.todo_empty') }}</p>
                    @else
                        <ol class="ps-3 mb-2 text-muted">
                            @foreach ($todoTasks as $task)
                                <li class="mb-2">
                                    <div class="d-inline-flex align-items-center gap-2 flex-wrap">
                                        <span>{{ $task->displayLabel() }}</span>
                                        @if ($url = $task->welcomeExecutionUrl())
                                            <a
                                                href="{{ $url }}"
                                                class="btn btn-link btn-sm p-0 text-primary flex-shrink-0 d-inline-flex align-items-center"
                                                title="{{ __('welcome_company.task_link_title') }}"
                                            >
                                                <span class="visually-hidden">{{ __('welcome_company.task_link_title') }}</span>
                                                <span class="icon-xs text-primary">
                                                    @svg('/duotone-icons/navigation/Arrow-right')
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                        <p class="small text-muted mb-4">
                            @if (auth()->user()->hasRoleForCurrentAccount('owner'))
                                {{ __('welcome_company.tasks_list_footer_before_link') }}
                                <a href="{{ route('account.tasks.index') }}" class="link-primary">{{ __('account.tasks_nav') }}</a>{{ __('welcome_company.tasks_list_footer_after_link') }}
                            @else
                                {{ __('welcome_company.tasks_list_footer_plain') }}
                            @endif
                        </p>
                    @endif

                    <a href="{{ route('account.dashboard') }}" class="btn btn-primary">
                        {{ __('welcome_company.cta') }}
                    </a>
                </div>

                <div class="col-lg-6 order-1 order-lg-2 text-center text-lg-end">
                    <div class="welcome-company-hero d-inline-block">
                        <div class="welcome-company-hero__wrap">
                            <img
                                src="{{ $welcomeRobot }}"
                                alt=""
                                class="img-fluid"
                                style="max-height: 420px; width: auto;"
                                loading="lazy"
                            />
                            @if($welcomeNameLine1)
                                <div class="welcome-company-hero__name">
                                    <span class="welcome-company-hero__name-line">{{ $welcomeNameLine1 }}</span>
                                    @if($welcomeNameLine2)
                                        <span class="welcome-company-hero__name-line">{{ $welcomeNameLine2 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
