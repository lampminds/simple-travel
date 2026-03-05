@extends('layouts.base', ['title' => $title ?? 'Prestadores - Simple Travel'])

@section('content')

    @include('layouts.partials.navbar', ['hideSearch' => true, 'fixedWidth' => true, 'sticky' => true, 'topbarColor' => 'navbar-light', 'classList' => 'ms-auto', 'ctaButtonClass' => 'btn-outline-secondary btn-sm'])

    {{-- Hero --}}
    <section class="hero-4 pb-5 pt-7 py-sm-7 bg-gradient2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="hero-title">Una forma más simple de trabajar con operadores turísticos</h1>
                    <p class="fs-17 text-muted">Simple Travel permite que prestadores y operadores trabajen sobre la misma información, reduciendo errores, tiempos de coordinación y tareas administrativas.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Intro + illustration placeholder --}}
    <section class="section py-6 position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 1: intro / problem (e.g. planillas, correos, mensajes) --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 280px; background: var(--bs-light, #f8f9fa); border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-1.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 pe-lg-5">
                    <p class="text-muted mb-3">Muchos prestadores gestionan sus servicios con planillas, correos y mensajes. Cuando trabajan con varios operadores, la coordinación se vuelve compleja: cambios de tarifas, confirmaciones, pagos y disponibilidad terminan dispersos en distintos canales.</p>
                    <p class="fw-semibold mb-0">Simple Travel organiza esa relación en un solo lugar.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Benefits: Qué puede esperar un prestador --}}
    <section class="section py-6 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Qué puede esperar un prestador</h2>

            <div class="row mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 2: pedidos claros --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 200px; background: #fff; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-2.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div>
                        <h3 class="h5 mb-3">Recibir pedidos claros y estructurados</h3>
                        <p class="text-muted mb-0">En lugar de múltiples mensajes o correos, los pedidos llegan organizados dentro del sistema, con fechas, pasajeros, servicios y condiciones claramente definidos. Esto reduce errores y facilita la confirmación de servicios.</p>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 3: tarifas y servicios --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 200px; background: #fff; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-3.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 d-flex align-items-center pe-lg-4">
                    <div>
                        <h3 class="h5 mb-3">Gestionar tarifas y servicios en un solo lugar</h3>
                        <p class="text-muted mb-0">Podés registrar tus servicios, recursos y tarifas dentro del sistema para utilizarlos en las operaciones con tus clientes operadores. Cuando la información está organizada, las cotizaciones y reservas fluyen mucho más rápido.</p>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 4: coordinación --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 200px; background: #fff; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-4.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div>
                        <h3 class="h5 mb-3">Mejor coordinación con tus clientes operadores</h3>
                        <p class="text-muted mb-0">El sistema permite mantener toda la operación documentada: solicitudes, confirmaciones, modificaciones y estados de cada servicio. Menos mensajes dispersos, más claridad en cada operación.</p>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 5: control --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 200px; background: #fff; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-5.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 d-flex align-items-center pe-lg-4">
                    <div>
                        <h3 class="h5 mb-3">Más control sobre tus operaciones</h3>
                        <p class="text-muted mb-0">Cada servicio queda registrado dentro del sistema, facilitando el seguimiento de reservas, fechas y compromisos operativos. Esto ayuda a evitar errores y mejora la organización interna.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 6: crecer --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 200px; background: #fff; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-6.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div>
                        <h3 class="h5 mb-3">Preparado para crecer</h3>
                        <p class="text-muted mb-0">Simple Travel está diseñado para conectar a operadores y prestadores de forma eficiente. Aunque hoy trabajes con uno o pocos operadores, contar con tu información organizada te permite escalar tu operación fácilmente.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cómo funciona + illustration placeholder --}}
    <section class="section py-6 position-relative">
        <div class="container">
            <h2 class="text-center mb-5">Cómo funciona para prestadores</h2>
            <div class="row align-items-start">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    {{-- Illustration placeholder 7: flujo / steps --}}
                    <div class="providers-illustration-placeholder rounded overflow-hidden" aria-hidden="true" style="min-height: 320px; background: var(--bs-light, #f8f9fa); border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.875rem;">
                        <img src="{{ asset('images/providers/providers-7.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <ol class="list-unstyled mb-0">
                        <li class="d-flex align-items-start mb-4">
                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 2rem; height: 2rem; font-weight: 600;">1</span>
                            <span>Un operador que utiliza Simple Travel te invita al sistema.</span>
                        </li>
                        <li class="d-flex align-items-start mb-4">
                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 2rem; height: 2rem; font-weight: 600;">2</span>
                            <span>Creás tu cuenta de prestador.</span>
                        </li>
                        <li class="d-flex align-items-start mb-4">
                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 2rem; height: 2rem; font-weight: 600;">3</span>
                            <span>Registrás tus servicios, recursos y tarifas.</span>
                        </li>
                        <li class="d-flex align-items-start mb-4">
                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 2rem; height: 2rem; font-weight: 600;">4</span>
                            <span>Los operadores pueden utilizarlos para cotizar y reservar dentro del sistema.</span>
                        </li>
                        <li class="d-flex align-items-start mb-4">
                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 2rem; height: 2rem; font-weight: 600;">5</span>
                            <span>Todo queda organizado y documentado en un solo lugar.</span>
                        </li>
                    </ol>
                    <p class="text-muted mt-2 mb-0"><strong>Menos coordinación manual. Más foco en brindar un gran servicio.</strong></p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section py-6 bg-light">
        <div class="container text-center">
            <p class="fs-17 text-muted mb-4">¿Trabajás con operadores que usan Simple Travel?</p>
            <p class="mb-4">Pediles una invitación para empezar.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Conocer Simple Travel</a>
        </div>
    </section>

    <x-site-footer />

@endsection
