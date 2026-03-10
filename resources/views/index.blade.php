@extends('layouts.app')

@section('content')
<main class="container py-4">
    <div class="row g-4">
        
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body text-center">
                    <img src="{{ asset('assets/img/usuario1.png') }}" class="rounded-circle mb-3 border border-3 border-white shadow" width="80" height="80" style="object-fit: cover;">
                    <h5 class="card-title fw-bold text-primary">Chef Principiante</h5>
                </div>
            </div>
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold border-0 pt-3">Categorías</div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"><span>🥗 Veganos</span></a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"><span>🥩 Carnívoros</span></a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"><span>🍰 Dulceros</span></a>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ asset('assets/img/usuario1.png') }}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 cursor-pointer" placeholder="¿Qué has cocinado hoy?">
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="#" class="btn btn-primary btn-sm px-4 rounded-pill text-white text-decoration-none">Publicar</a>
                    </div>
                </div>
            </div>

            <div id="feedContainer">
                
                @foreach($recetas as $receta)
                <article class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset($receta->avatar) }}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                            <div><h6 class="mb-0 fw-bold">{{ $receta->autor }}</h6></div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <p class="px-3 mb-2">{{ $receta->pasos }}</p>
                        <img src="{{ asset($receta->url_imagen) }}" class="w-100" style="max-height: 500px; object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">{{ $receta->titulo }}</h5>
                            <span class="badge bg-light text-dark border"><i class="bi bi-clock"></i> {{ $receta->tiempo_coccion }} min</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-primary flex-grow-1 rounded-pill text-white text-decoration-none">Ver Receta</a>
                        </div>
                    </div>
                </article>
                @endforeach

            </div>
        </div>

        <div class="col-lg-3 d-none d-lg-block">
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold border-0 pt-3">Populares</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item border-0 px-3 py-2">
                        <div class="fw-bold">Paella Valenciana</div>
                        <div class="text-muted d-flex align-items-center"><span class="text-warning me-1"><i class="bi bi-star-fill"></i> 4.8</span></div>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</main>
@endsection