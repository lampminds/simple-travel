@extends('layouts.base', ['title' => 'Ingresar'])

@section('content')
    <div class="bg-gradient2 min-vh-100 align-items-center d-flex justify-content-center pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-md-5 shadow">
                                    <div class="p-xl-5 p-3">
                                        <div class="mx-auto mb-5">
                                            <x-site-logo class="d-flex align-self-center" />
                                        </div>

                                        <h6 class="h5 mb-0 mt-3">Welcome back!</h6>
                                        <p class="text-muted mt-1 mb-4">
                                            Enter your email address and password to access admin panel.
                                        </p>
                                        <!--form start-->
                                        <form method="POST" action="{{ route('login') }}" class="authentication-form">

                                            @csrf
                                            @if (sizeof($errors) > 0)
                                                @foreach ($errors->all() as $error)
                                                    <p class="text-danger mb-3">{{ $error }}</p>
                                                @endforeach
                                            @endif

                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email <small>*</small></label>
                                                <input type="email" class="form-control" id="email"
                                                       placeholder="Email" name="email" value="{{ old('email') }}"/>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password">Password
                                                    <small>*</small></label>
                                                <a href="{{ route('password.request') }}"
                                                   class="float-end text-muted text-unline-dashed ms-1 fs-13">¿Olvidaste tu contraseña?</a>
                                                <input type="password" class="form-control" id="password"
                                                       name="password" placeholder="Contraseña" required/>
                                            </div>

                                            <div class="mb-3">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember"/>
                                                <label class="form-check-label text-muted" for="checkbox-signin">Recordarme</label>
                                            </div>

                                            <div class="mb-0 text-center d-grid">
                                                <button class="btn btn-primary" type="submit">Log In</button>
                                            </div>
                                        </form>
                                        <!--/.form end-->

                                        <div class="py-3 text-center"><span
                                                class="fs-13 fw-bold">OR</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <a href="" class="btn btn-white w-100">
                                                    <i data-feather="github" class='icon icon-xs me-2'></i>Github
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 offset-md-1 d-none d-md-inline-block">
                                    <div class="position-relative mt-5 pt-5">
                                        <!--swiper start-->
                                        <div class="swiper auth-swiper">
                                            <div class="swiper-wrapper">
                                                <div class="swiper-slide">

                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas1-{{ app()->getLocale() }}.png" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">Manage your saas
                                                                business with ease</h5>
                                                            <p class="text-muted">Make your saas application
                                                                stand out with high-quality landing page
                                                                designed and developed by professional.</p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- swiper-slide End -->
                                                <div class="swiper-slide">

                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas2.jpg" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">The best way to showcase
                                                                your mobile app</h5>
                                                            <p class="text-muted">Sed ut perspiciatis unde omnis
                                                                iste natus error sit voluptatem accusantium.
                                                            </p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- swiper-slide End -->
                                                <div class="swiper-slide">

                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <img src="/images/hero/saas3.jpg" alt=""
                                                                 class="w-75"/>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center my-4 pb-5">
                                                        <div class="col">
                                                            <h5 class="fw-medium fs-16">Smart Solution that
                                                                convert Lead to Customer</h5>
                                                            <p class="text-muted">Sed ut perspiciatis unde omnis
                                                                iste natus error sit voluptatem accusantium.
                                                            </p>
                                                        </div>

                                                    </div>
                                                </div>
                                                <!-- swiper-slide End -->
                                            </div>
                                            <!-- swiper-wrapper End -->
                                            <div class="swiper-pagination"></div>
                                        </div>
                                        <!--swiper end-->
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">¿No tenés cuenta? <a href="{{ route('register') }}"
                                    class="text-primary fw-semibold ms-1">Crear cuenta</a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection
