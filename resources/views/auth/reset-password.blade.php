@extends('layouts.base', ['title' => 'Restablecer contraseña'])

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

                                <h6 class="h5 mb-0 mt-3">Nueva contraseña</h6>
                                <p class="text-muted mt-1 mb-4">Ingresá tu email y la nueva contraseña.</p>

                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <p class="text-danger mb-3">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="{{ route('password.update') }}" class="authentication-form">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <small>*</small></label>
                                        <input type="email" class="form-control" id="email"
                                               name="email" value="{{ old('email', $request->email) }}"
                                               required autofocus/>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nueva contraseña <small>*</small></label>
                                        <input type="password" class="form-control" id="password"
                                               name="password" placeholder="Mínimo 8 caracteres" required/>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar contraseña <small>*</small></label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                               name="password_confirmation" placeholder="Repetí la contraseña" required/>
                                    </div>

                                    <div class="mb-0 text-center pt-3 d-grid">
                                        <button class="btn btn-primary" type="submit">Restablecer contraseña</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Volver a <a href="{{ route('login') }}"
                                                             class="text-primary fw-semibold ms-1">Ingresar</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
