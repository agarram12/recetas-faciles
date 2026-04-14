@extends('layouts.app')

@section('content')
<main class="container py-4">
    <div class="row g-4">
        
        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 100px;">

                @auth
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset(Auth::user()->avatar) }}" class="rounded-circle mb-3 border border-3 border-white shadow" width="80" height="80" style="object-fit: cover;">
                        <h5 class="card-title fw-bold" style="color: #729c48;">{{ Auth::user()->name }}</h5>
                    </div>
                </div>
                @else
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/img/logo.png') }}" class="rounded-circle mb-3 border border-3 border-white shadow" width="80" height="80" style="object-fit: cover;">
                        <h5 class="card-title fw-bold text-muted">¡Bienvenido!</h5>
                        <p class="text-muted small mb-0">Inicia sesión para publicar tus propias recetas.</p>
                    </div>
                </div>
                @endauth

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">Categorías</div>
                    <div class="list-group list-group-flush">
                        <a href="/?buscar=Veganos" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"><span>🥗 Veganos</span></a>
                        <a href="/?buscar=Carnívoros" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"><span>🥩 Carnívoros</span></a>
                        <a href="/?buscar=Dulceros" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center"><span>🍰 Dulceros</span></a>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-6 col-12">
            
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ asset(Auth::check() ? Auth::user()->avatar : 'assets/img/logo.png') }}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 cursor-pointer" placeholder="¿Qué has cocinado hoy?" onclick="window.location.href='{{ route("receta.create") }}'">
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('receta.create') }}" class="btn btn-sm px-4 rounded-pill text-white text-decoration-none" style="background-color: #729c48;">Publicar</a>
                    </div>
                </div>
            </div>

            @if(request('buscar'))
                <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #eaf3e3; color: #4e6e2e;">
                    <i class="bi bi-search me-2"></i> Mostrando resultados para: <strong>"{{ request('buscar') }}"</strong>
                    <a href="/" class="float-end text-decoration-none" style="color: #729c48;">Limpiar filtro <i class="bi bi-x-circle"></i></a>
                </div>
            @else
                <div class="mb-3">
                    <h5 class="fw-bold">Feed social</h5>
                    <p class="text-muted small">Recetas de las personas que sigues y tus publicaciones recientes.</p>
                </div>
            @endif

            @if($recetas->count() == 0)
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown display-4 text-muted mb-3"></i>
                    <h4 class="text-muted">No hay recetas para mostrar</h4>
                    <p class="text-muted">Prueba con otra búsqueda o sigue a nuevos usuarios.</p>
                </div>
            @endif

            <div id="feedContainer" class="row g-3">

                @foreach($recetas as $receta)
                <div class="col-md-6">
                    <article class="card h-100 border-0 shadow-sm">
                        
                        <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($receta->autor_avatar ?? 'assets/img/logo.png') }}" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">
                                        <a href="{{ route('usuario.show', $receta->autor_id) }}" class="text-decoration-none text-dark">
                                            {{ $receta->autor_nombre }}
                                        </a>
                                    </h6>
                                </div>
                            </div>

                            <span class="badge bg-light text-dark border">{{ $receta->categoria_nombre }}</span>

                            @if(Auth::check() && Auth::id() == $receta->usuario_id)
                            <div class="d-flex gap-2">
                                <a href="{{ route('receta.edit', $receta->id) }}" class="btn btn-link text-primary p-0 border-0 text-decoration-none">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('receta.destroy', $receta->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar esta receta?');" class="m-0">
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
                                <span class="badge bg-light text-dark border"><i class="bi bi-clock"></i> {{ $receta->tiempo_coccion }}'</span>
                            </div>

                            <div class="d-flex gap-2 mt-auto">

                                <a href="{{ route('receta.show', $receta->id) }}" class="btn btn-outline-success btn-sm rounded-pill w-50">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                @auth
                                    @php
                                        $esFavorito = auth()->user()->recetasFavoritas()->where('receta_id', $receta->id)->exists();
                                    @endphp
                                    <form action="{{ route('receta.favorito', $receta->id) }}" method="POST" class="w-50 m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm rounded-pill w-100 {{ $esFavorito ? 'btn-danger text-white' : 'btn-outline-danger' }}">
                                            <i class="bi {{ $esFavorito ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm rounded-pill w-50">
                                        <i class="bi bi-heart"></i>
                                    </a>
                                @endauth

                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $recetas->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 100px;">

                <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #333;">Populares</h5>
                        
                        @foreach($populares as $plato)
                            <a href="{{ route('receta.show', $plato->id) }}" class="text-decoration-none d-block mb-3 p-2 rounded popular-item" style="transition: all 0.2s ease;">
                                <h6 class="fw-bold mb-1 text-dark">{{ $plato->titulo }}</h6>
                                <div class="d-flex align-items-center gap-1" style="color: #eab308; font-size: 0.95rem;">
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-muted fw-bold ms-1">{{ round($plato->valoraciones_avg_puntuacion, 1) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<style>
    .popular-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
</style>
@endsection