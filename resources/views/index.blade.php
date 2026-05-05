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

                {{-- Filtros activos --}}
                @if($categoria || $dificultad || $tiempo || $orden !== 'recientes')
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-muted"><i class="bi bi-funnel"></i> Filtros activos</span>
                            <a href="/" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" style="font-size: 0.75rem;">
                                <i class="bi bi-x-lg"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Categorías dinámicas --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">
                        <i class="bi bi-bookmark" style="color: #729c48;"></i> Categorías
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($categorias as $cat)
                        @php
                            $iconos = ['Veganos' => '🥗', 'Carnívoros' => '🥩', 'Dulceros' => '🍰'];
                            $icono = $iconos[$cat->nombre] ?? '🍽️';
                            $activa = $categoria == $cat->id;
                        @endphp
                        <a href="/?categoria={{ $cat->id }}{{ $dificultad ? '&dificultad='.$dificultad : '' }}{{ $tiempo ? '&tiempo='.$tiempo : '' }}{{ $orden !== 'recientes' ? '&orden='.$orden : '' }}" 
                           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activa ? 'active' : '' }}"
                           style="{{ $activa ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
                            <span>{{ $icono }} {{ $cat->nombre }}</span>
                            @if($activa)
                                <i class="bi bi-check-circle-fill"></i>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Dificultad --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">
                        <i class="bi bi-speedometer2" style="color: #729c48;"></i> Dificultad
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach(['Fácil' => '🟢', 'Media' => '🟡', 'Difícil' => '🔴'] as $nivel => $color)
                        @php $activaD = $dificultad === $nivel; @endphp
                        <a href="/?dificultad={{ $nivel }}{{ $categoria ? '&categoria='.$categoria : '' }}{{ $tiempo ? '&tiempo='.$tiempo : '' }}{{ $orden !== 'recientes' ? '&orden='.$orden : '' }}" 
                           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activaD ? 'active' : '' }}"
                           style="{{ $activaD ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
                            <span>{{ $color }} {{ $nivel }}</span>
                            @if($activaD)
                                <i class="bi bi-check-circle-fill"></i>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Tiempo de cocción --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">
                        <i class="bi bi-clock" style="color: #729c48;"></i> Tiempo
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach(['rapido' => '⚡ Rápido (≤15 min)', 'medio' => '🕐 Medio (16-45 min)', 'largo' => '🕑 Largo (46-90 min)', 'elaborado' => '👨‍🍳 Elaborado (+90 min)'] as $clave => $etiqueta)
                        @php $activaT = $tiempo === $clave; @endphp
                        <a href="/?tiempo={{ $clave }}{{ $categoria ? '&categoria='.$categoria : '' }}{{ $dificultad ? '&dificultad='.$dificultad : '' }}{{ $orden !== 'recientes' ? '&orden='.$orden : '' }}" 
                           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activaT ? 'active' : '' }}"
                           style="{{ $activaT ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
                            <span>{{ $etiqueta }}</span>
                            @if($activaT)
                                <i class="bi bi-check-circle-fill"></i>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Ordenar por --}}
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">
                        <i class="bi bi-sort-down" style="color: #729c48;"></i> Ordenar por
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach(['recientes' => '🆕 Más recientes', 'antiguos' => '📅 Más antiguos', 'rapidos' => '⏱️ Menos tiempo', 'lentos' => '🍲 Más tiempo'] as $clave => $etiqueta)
                        @php $activaO = $orden === $clave; @endphp
                        <a href="/?orden={{ $clave }}{{ $categoria ? '&categoria='.$categoria : '' }}{{ $dificultad ? '&dificultad='.$dificultad : '' }}{{ $tiempo ? '&tiempo='.$tiempo : '' }}" 
                           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activaO ? 'active' : '' }}"
                           style="{{ $activaO ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
                            <span>{{ $etiqueta }}</span>
                            @if($activaO)
                                <i class="bi bi-check-circle-fill"></i>
                            @endif
                        </a>
                        @endforeach
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
            @endif

            @if($categoria || $dificultad || $tiempo)
                <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center flex-wrap gap-2" style="background-color: #eaf3e3; color: #4e6e2e; border-radius: 12px;">
                    <i class="bi bi-funnel me-1"></i> <span class="fw-bold">Filtrando:</span>
                    @if($categoria)
                        @php $catNombre = $categorias->firstWhere('id', $categoria)->nombre ?? ''; @endphp
                        <span class="badge rounded-pill text-white px-3 py-1" style="background-color: #729c48;">{{ $catNombre }}</span>
                    @endif
                    @if($dificultad)
                        <span class="badge rounded-pill text-white px-3 py-1" style="background-color: #729c48;">{{ $dificultad }}</span>
                    @endif
                    @if($tiempo)
                        @php
                            $tiempoLabels = ['rapido' => '≤15 min', 'medio' => '16-45 min', 'largo' => '46-90 min', 'elaborado' => '+90 min'];
                        @endphp
                        <span class="badge rounded-pill text-white px-3 py-1" style="background-color: #729c48;">{{ $tiempoLabels[$tiempo] ?? $tiempo }}</span>
                    @endif
                    <a href="/" class="ms-auto text-decoration-none fw-bold" style="color: #729c48;">Limpiar <i class="bi bi-x-circle"></i></a>
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