@extends('layouts.base', ['title' => 'Prompt - Forgot Password'])

@section('content')
    <div class="bg-gradient2 min-vh-100 align-items-center d-flex justify-content-center pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="p-xl-5 p-3">
                                <div class="mx-auto mb-5">
                                    <x-site-logo class="d-flex align-self-center" />
                                </div>

                                <h6 class="h5 mb-0 mt-3">Recuperar contraseña</h6>
                                <p class="text-muted mt-1 mb-4">Ingresá tu email y te enviamos un enlace para restablecer tu contraseña.</p>

                                @if (session('status'))
                                    <div class="alert alert-success mb-3">{{ session('status') }}</div>
                                @endif
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <p class="text-danger mb-3">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="{{ route('password.email') }}" class="authentication-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <small>*</small></label>
                                        <input type="email" class="form-control" id="email"
                                               placeholder="Email" name="email" value="{{ old('email') }}" required autofocus/>
                                    </div>

                                    <div class="mb-0 text-center pt-3 d-grid">
                                        <button class="btn btn-primary" type="submit">Enviar enlace</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Volver a <a href="{{ route('login') }}"
                                                             class="text-primary fw-semibold ms-1">Ingresar</a></p>
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
