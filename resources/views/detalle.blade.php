@extends('layouts.app')

@section('content')
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm mb-4">
                <img src="{{ asset($receta->url_imagen) }}" class="card-img-top" style="max-height: 400px; object-fit: cover;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fw-bold mb-0">{{ $receta->titulo }}</h2>
                        <span class="badge bg-primary">{{ $receta->categoria }}</span>
                    </div>
                    <p class="text-muted"><i class="bi bi-person-circle"></i> Por <strong>{{ $receta->autor }}</strong></p>
                    
                    <div class="d-flex gap-3 mb-4 bg-light p-3 rounded">
                        <div><i class="bi bi-clock"></i> {{ $receta->tiempo_coccion }} min</div>
                        <div><i class="bi bi-bar-chart"></i> {{ $receta->dificultad }}</div>
                    </div>

                    <h5 class="fw-bold"><i class="bi bi-list-ol"></i> Pasos</h5>
                    @php
                        $lista_pasos = array_filter(explode('.', $receta->pasos));
                    @endphp

                    <ul class="list-group list-group-flush mb-4">
                        @foreach($lista_pasos as $index => $paso)
                            @if(trim($paso) != '')
                                <li class="list-group-item bg-light border-0 mb-2 p-3 rounded shadow-sm d-flex align-items-start">
                                    <span class="badge bg-success fs-6 me-3 rounded-circle">{{ $index + 1 }}</span>
                                    <span class="fs-6">{{ trim($paso) }}.</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="mt-4 text-end">
                        <a href="/" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-left"></i> Volver al Inicio</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection