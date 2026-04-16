@extends('layouts.base', ['title' => $title])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ $heading }}</h3>
                        <p class="mt-1 fw-medium">{{ $intro }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle bg-white rounded border">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">{{ $relatedLabel }}</th>
                                    <th scope="col" class="text-end">Servicios que utiliza</th>
                                    <th scope="col" class="text-end">Servicios reservados</th>
                                    <th scope="col" class="text-end">Servicios a liquidar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row['company'] }}</td>
                                        <td class="text-end">{{ $row['services_count'] }}</td>
                                        <td class="text-end">{{ $row['booked_count'] }}</td>
                                        <td class="text-end">{{ $row['to_settle_count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if (! $isProvider)
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <a href="{{ route('account.invitations.index') }}" class="btn btn-primary">
                            Invitar
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <x-site-footer-simple />
@endsection
