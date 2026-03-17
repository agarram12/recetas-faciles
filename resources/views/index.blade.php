@extends('layouts.app')

@section('content')
<main class="container py-4">
    <div class="row g-4">
        <!-- Columna izquierda: Perfil y categorías -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 100px;">

                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset($usuario_actual->avatar) }}" class="rounded-circle mb-3 border border-3 border-white shadow" width="80" height="80" style="object-fit: cover;">
                        <h5 class="card-title fw-bold text-success">{{ $usuario_actual->name }}</h5>
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
        </div>

        <div class="col-lg-6 col-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ asset('assets/img/usuario1.png') }}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 cursor-pointer" placeholder="¿Qué has cocinado hoy?">
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('receta.create') }}" class="btn btn-success btn-sm px-4 rounded-pill text-white text-decoration-none">Publicar</a>
                    </div>
                </div>
            </div>

            <div id="feedContainer" class="row g-3">

                @foreach($recetas as $receta)
                <div class="col-md-6">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($receta->avatar) }}" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">{{ $receta->autor }}</h6>
                                </div>
                            </div>

                            @if($receta->usuario_id == $usuario_actual->id)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('receta.edit', $receta->id) }}" class="btn btn-link text-primary p-0 border-0 text-decoration-none">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('receta.destroy', $receta->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar esta receta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 border-0 text-decoration-none">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-0">
                            <img src="{{ asset($receta->url_imagen) }}" class="w-100" style="height: 200px; object-fit: cover;">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 70%;">{{ $receta->titulo }}</h6>
                                <span class="badge bg-light text-dark border"><i class="bi bi-clock"></i> {{ $receta->tiempo_preparacion }}'</span>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn btn-outline-danger btn-sm rounded-pill w-50">
                                    <i class="bi bi-heart"></i>
                                </button>

                                <a href="{{ route('receta.show', $receta->id) }}" class="btn btn-success btn-sm rounded-pill w-50 text-white text-decoration-none">
                                    Ver &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach

            </div>
        </div>
        <!-- Columna derecha: Populares -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 100px;">

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

    </div>
</main>
@endsection