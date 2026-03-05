@extends('layouts.base', ['title' => 'Confirmar email'])

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

                                <h6 class="h5 mb-0 mt-3">Confirmá tu email</h6>
                                <p class="text-muted mt-1 mb-4">
                                    Te enviamos un email de bienvenida con un enlace para verificar tu dirección. Hacé clic en ese enlace para activar tu cuenta.
                                </p>

                                @if (session('status') == 'verification-link-sent')
                                    <div class="alert alert-success">
                                        Te enviamos un nuevo enlace de verificación a tu email.
                                    </div>
                                @endif

                                <p class="text-muted mb-4">
                                    Si no recibiste el email, podés pedir que te reenviemos uno.
                                </p>

                                <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        Reenviar email de verificación
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <a href="{{ route('account.dashboard') }}" class="btn btn-primary">
                                Continuar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
