@extends('layouts.app')

@section('content')
<main class="container py-4">
    <div class="row g-4">
        
        {{-- SIDEBAR IZQUIERDO: Filtros (desktop) --}}
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

                @include('partials.filtros-sidebar')

            </div>
        </div>

        {{-- CONTENIDO CENTRAL: Feed --}}
        <div class="col-lg-6 col-12">
            
            {{-- Botón filtros en móvil --}}
            <div class="d-lg-none mb-3">
                <button class="btn btn-outline-gris w-100 rounded-pill" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros">
                    <i class="bi bi-funnel"></i> Filtros y categorías
                    @if($categoria || $dificultad || $tiempo || $orden !== 'recientes')
                        <span class="badge rounded-pill text-white ms-2" style="background-color: #729c48;">Activos</span>
                    @endif
                </button>
            </div>

            {{-- CTA Publicar --}}
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

            {{-- Alertas de búsqueda/filtros activos --}}
            <div id="alertaBusqueda">
            @if(request('buscar'))
                <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #eaf3e3; color: #4e6e2e;">
                    <i class="bi bi-search me-2"></i> Mostrando resultados para: <strong>"{{ request('buscar') }}"</strong>
                    <a href="/" class="float-end text-decoration-none btn-limpiar-busqueda" style="color: #729c48;">Limpiar filtro <i class="bi bi-x-circle"></i></a>
                </div>
            @endif
            </div>

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
                        @php $tiempoLabels = ['rapido' => '≤15 min', 'medio' => '16-45 min', 'largo' => '46-90 min', 'elaborado' => '+90 min']; @endphp
                        <span class="badge rounded-pill text-white px-3 py-1" style="background-color: #729c48;">{{ $tiempoLabels[$tiempo] ?? $tiempo }}</span>
                    @endif
                    <a href="/" class="ms-auto text-decoration-none fw-bold" style="color: #729c48;">Limpiar <i class="bi bi-x-circle"></i></a>
                </div>
            @endif

            {{-- Estado vacío --}}
            @if($recetas->count() == 0)
                <div class="text-center py-5" id="feedVacio">
                    <i class="bi bi-emoji-frown display-4 text-muted mb-3"></i>
                    <h4 class="text-muted">No hay recetas para mostrar</h4>
                    <p class="text-muted">Prueba con otra búsqueda o sigue a nuevos usuarios.</p>
                </div>
            @endif

            {{-- Feed de recetas --}}
            <div id="feedContainer" class="row g-3">
                @foreach($recetas as $receta)
                    @include('partials.receta-card', ['receta' => $receta, 'favoritoIds' => $favoritoIds])
                @endforeach
            </div>

            {{-- Infinite scroll loader --}}
            @if($recetas->hasMorePages())
            <div id="infinite-loader" class="text-center py-4">
                <div class="spinner-border text-success" role="status" style="width: 2rem; height: 2rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted small mt-2 mb-0">Cargando más recetas...</p>
            </div>
            @endif

            {{-- Fin del feed --}}
            <div id="feed-end" class="text-center py-3 d-none">
                <p class="text-muted small mb-0"><i class="bi bi-check-circle"></i> Has visto todas las recetas</p>
            </div>
        </div>

        {{-- SIDEBAR DERECHO: Populares --}}
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

{{-- Offcanvas de filtros para móvil --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFiltros" aria-labelledby="offcanvasFiltrosLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="offcanvasFiltrosLabel" style="color: #729c48;">
            <i class="bi bi-funnel"></i> Filtros
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        @include('partials.filtros-sidebar')
    </div>
</div>

<style>
    .popular-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    /* Animación de entrada de cards */
    .receta-card-item {
        animation: fadeInUp 0.4s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stagger animation for initial load */
    .receta-card-item:nth-child(1) { animation-delay: 0s; }
    .receta-card-item:nth-child(2) { animation-delay: 0.05s; }
    .receta-card-item:nth-child(3) { animation-delay: 0.1s; }
    .receta-card-item:nth-child(4) { animation-delay: 0.15s; }
    .receta-card-item:nth-child(5) { animation-delay: 0.2s; }
    .receta-card-item:nth-child(6) { animation-delay: 0.25s; }
    .receta-card-item:nth-child(7) { animation-delay: 0.3s; }
    .receta-card-item:nth-child(8) { animation-delay: 0.35s; }

    /* Spinner de búsqueda (dentro del feed) */
    .search-spinner {
        display: none;
        width: 1rem;
        height: 1rem;
    }
    .search-spinner.active {
        display: inline-block;
    }

    /* Skeleton loader para feed */
    .feed-loading-state {
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const feedContainer = document.getElementById('feedContainer');
    const infiniteLoader = document.getElementById('infinite-loader');
    const feedEnd = document.getElementById('feed-end');
    const alertaBusqueda = document.getElementById('alertaBusqueda');
    const feedVacio = document.getElementById('feedVacio');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    let currentPage = 1;
    let isLoading = false;
    let hasMore = {{ $recetas->hasMorePages() ? 'true' : 'false' }};

    // ============================================
    // INFINITE SCROLL con IntersectionObserver
    // ============================================
    if (infiniteLoader) {
        const observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                cargarMasRecetas();
            }
        }, { rootMargin: '200px' });

        observer.observe(infiniteLoader);
    }

    function cargarMasRecetas() {
        if (isLoading || !hasMore) return;
        isLoading = true;
        currentPage++;

        // Construir URL con los filtros actuales
        const params = new URLSearchParams(window.location.search);
        params.set('page', currentPage);

        fetch('/?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.html && data.html.trim()) {
                // Insertar con stagger animation
                const temp = document.createElement('div');
                temp.innerHTML = data.html;
                const newCards = temp.querySelectorAll('.receta-card-item');
                
                newCards.forEach(function(card, i) {
                    card.style.opacity = '0';
                    card.style.animationDelay = (i * 0.05) + 's';
                    feedContainer.appendChild(card);
                });

                // Bind AJAX favoritos a los nuevos botones
                bindFavoritos();
            }

            hasMore = data.hasMore;
            isLoading = false;

            if (!hasMore) {
                infiniteLoader.classList.add('d-none');
                feedEnd.classList.remove('d-none');
            }
        })
        .catch(err => {
            console.error('Error cargando recetas:', err);
            isLoading = false;
        });
    }

    // ============================================
    // BÚSQUEDA AJAX con debounce (sin recargas)
    // ============================================
    const searchInput = document.getElementById('searchInputNav');
    let searchTimeout = null;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            // Mostrar spinner
            const spinner = document.getElementById('searchSpinner');
            if (spinner) spinner.classList.add('active');

            searchTimeout = setTimeout(function() {
                if (query.length >= 2) {
                    buscarAJAX(query);
                } else if (query.length === 0) {
                    // Restaurar feed SIN recarga de página
                    restaurarFeed();
                }
                if (spinner) spinner.classList.remove('active');
            }, 400);
        });
    }

    // Búsqueda AJAX sin recargar
    function buscarAJAX(query) {
        // Mostrar loading
        feedContainer.classList.add('feed-loading-state');
        
        // Actualizar URL sin recargar
        const url = new URL(window.location);
        url.searchParams.set('buscar', query);
        history.pushState({feedSearch: true, buscar: query}, '', url);

        fetch('/?buscar=' + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            feedContainer.innerHTML = data.html || '';
            feedContainer.classList.remove('feed-loading-state');
            
            hasMore = data.hasMore;
            currentPage = 1;
            
            if (infiniteLoader) {
                infiniteLoader.classList.toggle('d-none', !hasMore);
            }
            if (feedEnd) {
                feedEnd.classList.toggle('d-none', hasMore || data.total === 0);
            }

            // Actualizar alerta de búsqueda
            actualizarAlertaBusqueda(query, data.total);

            // Ocultar/mostrar estado vacío
            if (feedVacio) {
                feedVacio.classList.toggle('d-none', data.total > 0);
            }

            // Mostrar contador de resultados
            if (data.total !== undefined) {
                mostrarToast(data.total + ' receta(s) encontrada(s)', 'info');
            }

            // Bind favoritos en nuevos elementos
            bindFavoritos();
        })
        .catch(err => {
            console.error('Error en búsqueda:', err);
            feedContainer.classList.remove('feed-loading-state');
        });
    }

    // Restaurar feed original sin recargar
    function restaurarFeed() {
        feedContainer.classList.add('feed-loading-state');

        // Limpiar URL
        const url = new URL(window.location);
        url.searchParams.delete('buscar');
        history.pushState({feedSearch: true}, '', url);

        fetch('/?' + url.searchParams.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            feedContainer.innerHTML = data.html || '';
            feedContainer.classList.remove('feed-loading-state');
            
            hasMore = data.hasMore;
            currentPage = 1;

            if (infiniteLoader) {
                infiniteLoader.classList.toggle('d-none', !hasMore);
            }
            if (feedEnd) {
                feedEnd.classList.toggle('d-none', true);
            }

            // Limpiar alerta de búsqueda
            if (alertaBusqueda) alertaBusqueda.innerHTML = '';
            if (feedVacio) feedVacio.classList.toggle('d-none', (data.total || 0) > 0);

            bindFavoritos();
        })
        .catch(err => {
            console.error('Error restaurando feed:', err);
            feedContainer.classList.remove('feed-loading-state');
        });
    }

    // Actualizar alerta de búsqueda dinámicamente
    function actualizarAlertaBusqueda(query, total) {
        if (!alertaBusqueda) return;
        if (query && query.length > 0) {
            alertaBusqueda.innerHTML = `
                <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #eaf3e3; color: #4e6e2e;">
                    <i class="bi bi-search me-2"></i> Mostrando ${total} resultado(s) para: <strong>"${query}"</strong>
                    <a href="#" class="float-end text-decoration-none btn-limpiar-busqueda" style="color: #729c48;" onclick="event.preventDefault(); document.getElementById('searchInputNav').value=''; document.getElementById('searchInputNav').dispatchEvent(new Event('input'));">
                        Limpiar filtro <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            `;
        } else {
            alertaBusqueda.innerHTML = '';
        }
    }

    // Manejar botón atrás del navegador (solo para búsquedas AJAX que usamos pushState)
    window.addEventListener('popstate', function(e) {
        // Solo manejar estados que nosotros empujamos con pushState
        if (!e.state || !e.state.feedSearch) return;
        
        const params = new URLSearchParams(window.location.search);
        const buscar = params.get('buscar') || '';
        if (searchInput) searchInput.value = buscar;
        if (buscar.length >= 2) {
            buscarAJAX(buscar);
        } else {
            restaurarFeed();
        }
    });

    // ============================================
    // FAVORITOS sin recarga (AJAX)
    // ============================================
    function bindFavoritos() {
        document.querySelectorAll('.btn-favorito').forEach(function(btn) {
            // Evitar doble binding
            if (btn.dataset.bound) return;
            btn.dataset.bound = 'true';

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;
                const boton = this;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // Actualizar apariencia del botón
                    const icono = boton.querySelector('i');
                    if (data.esFavorito) {
                        boton.classList.remove('btn-outline-danger');
                        boton.classList.add('btn-danger', 'text-white');
                        icono.classList.remove('bi-heart');
                        icono.classList.add('bi-heart-fill');
                    } else {
                        boton.classList.remove('btn-danger', 'text-white');
                        boton.classList.add('btn-outline-danger');
                        icono.classList.remove('bi-heart-fill');
                        icono.classList.add('bi-heart');
                    }

                    // Toast de confirmación
                    mostrarToast(data.mensaje, 'success');
                })
                .catch(err => {
                    console.error('Error favorito:', err);
                    mostrarToast('Error al actualizar favoritos', 'danger');
                });
            });
        });
    }

    // Bind inicial
    bindFavoritos();
});
</script>
@endsection