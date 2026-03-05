@extends('layouts.base', ['title' => 'Crear cuenta'])

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

                                        <h6 class="h5 mb-0 mt-3">Crear cuenta</h6>
                                        <p class="text-muted mt-1 mb-4">Completá los datos de tu empresa y personales para registrarte.</p>

                                        <div class="alert alert-warning border-0 mb-4" role="alert">
                                            <small>
                                                <strong>Importante:</strong> Si ya existe una cuenta para tu empresa no debes registrarte aquí sino desde una invitación del administrador de tu empresa (esto lo implementaremos más adelante).
                                            </small>
                                        </div>

                                        <form method="POST" action="{{ route('register') }}" class="authentication-form">
                                            @csrf
                                            @if ($errors->any())
                                                @foreach ($errors->all() as $error)
                                                    <p class="text-danger mb-3">{{ $error }}</p>
                                                @endforeach
                                            @endif

                                            <div class="mb-3">
                                                <label for="company_name" class="form-label">Nombre de empresa <small>*</small></label>
                                                <input type="text" class="form-control" id="company_name"
                                                       placeholder="Nombre de tu empresa" name="company_name"
                                                       value="{{ old('company_name') }}" required autofocus/>
                                            </div>

                                            <div class="mb-3">
                                                <label for="company_type" class="form-label">Tipo de empresa <small>*</small></label>
                                                <select class="form-select" id="company_type" name="company_type" required>
                                                    <option value="">Seleccionar tipo...</option>
                                                    @foreach($companyTypes ?? [] as $id => $data)
                                                        @php
                                                            $name = is_array($data) ? ($data['name'] ?? '') : $data;
                                                            $description = is_array($data) ? ($data['description'] ?? '') : '';
                                                        @endphp
                                                        <option value="{{ $id }}" {{ old('company_type') == $id ? 'selected' : '' }}
                                                                title="{{ e($description) }}"
                                                                data-description="{{ e($description) }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <div id="company_type_description" class="small text-muted mt-2" style="min-height: 1.5em;" aria-live="polite"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="name" class="form-label">Tu nombre <small>*</small></label>
                                                <input type="text" class="form-control" id="name"
                                                       placeholder="Tu nombre" name="name"
                                                       value="{{ old('name') }}" required autofocus/>
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email <small>*</small></label>
                                                <input type="email" class="form-control" id="email"
                                                       placeholder="Email" name="email"
                                                       value="{{ old('email') }}" required/>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Contraseña <small>*</small></label>
                                                <input type="password" class="form-control" id="password"
                                                       name="password" placeholder="Mínimo 8 caracteres" required/>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirmar contraseña <small>*</small></label>
                                                <input type="password" class="form-control" id="password_confirmation"
                                                       name="password_confirmation" placeholder="Repetí la contraseña" required/>
                                            </div>

                                            <div class="mb-0 text-center d-grid">
                                                <button class="btn btn-primary" type="submit">Crear cuenta</button>
                                            </div>
                                        </form>
                                        <div class="py-3 text-center"><span
                                                class="fs-13 fw-bold">OR</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <a href="" class="btn btn-white w-100">
                                                    <i data-feather="github" class='icon icon-xs me-2'></i>Sign Up with
                                                    Github</a>
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
                            <p class="text-muted">¿Ya tenés cuenta? <a href="{{ route('login') }}"
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

@section('script-bottom')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('company_type');
    const descEl = document.getElementById('company_type_description');
    if (!select || !descEl) return;

    function updateDescription() {
        const opt = select.options[select.selectedIndex];
        const desc = opt?.dataset?.description || '';
        descEl.textContent = desc;
    }

    select.addEventListener('change', updateDescription);
    updateDescription(); // Initial state (e.g. when old('company_type') pre-selects)
});
</script>
@endsection

