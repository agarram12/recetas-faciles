@extends('layouts.app')

@section('content')
<main class="container py-5">
    
    <div class="row mb-5 align-items-center bg-white p-4 rounded-4 shadow-sm border-0">
        <div class="col-auto">
            <img src="{{ asset(Auth::user()->avatar ?? 'assets/img/logo.png') }}" class="rounded-circle shadow-sm border border-3 border-white" width="100" height="100" style="object-fit: cover;">
        </div>
        <div class="col">
            <h2 class="fw-bold mb-0" style="color: #729c48;">¡Hola, {{ Auth::user()->name }}!</h2>
            <p class="text-muted mb-0"><i class="bi bi-envelope"></i> {{ Auth::user()->email }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary rounded-pill shadow-sm"><i class="bi bi-gear"></i> Ajustes</a>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav nav-pills mb-4 gap-2" id="perfilTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold" id="favoritos-tab" data-bs-toggle="pill" data-bs-target="#favoritos" type="button" role="tab" style="background-color: #729c48;">
                <i class="bi bi-heart-fill me-1"></i> Mis Favoritos ({{ count($misFavoritos) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold text-secondary bg-white border shadow-sm" id="mis-recetas-tab" data-bs-toggle="pill" data-bs-target="#mis-recetas" type="button" role="tab">
                <i class="bi bi-journal-text me-1"></i> Mis Publicaciones ({{ count($misRecetas) }})
            </button>
        </li>
    </ul>

    {{-- Contenido de las Pestañas --}}
    <div class="tab-content" id="perfilTabsContent">
        
        <div class="tab-pane fade show active" id="favoritos" role="tabpanel">
            <div class="row g-4">
                @forelse($misFavoritos as $receta)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <img src="{{ asset($receta->url_imagen) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge bg-light text-dark mb-2">{{ $receta->categoria->nombre ?? 'Sin categoría' }}</span>
                            <h6 class="fw-bold text-truncate">{{ $receta->titulo }}</h6>
                            <p class="text-muted small mb-3"><i class="bi bi-person"></i> {{ $receta->autor->name ?? 'Anónimo' }}</p>
                            <a href="{{ route('receta.show', $receta->id) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill">Ver Receta</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-heart-break display-4 text-muted mb-3"></i>
                    <h5 class="text-muted fw-bold">Aún no tienes recetas favoritas</h5>
                    <p class="text-muted mb-0">Explora el inicio y guarda las que más te gusten.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="mis-recetas" role="tabpanel">
             <div class="row g-4">
                @forelse($misRecetas as $receta)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <img src="{{ asset($receta->url_imagen) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge bg-light text-dark mb-2">{{ $receta->categoria->nombre ?? 'Sin categoría' }}</span>
                            <h6 class="fw-bold text-truncate">{{ $receta->titulo }}</h6>
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('receta.show', $receta->id) }}" class="btn btn-sm btn-outline-secondary w-50 rounded-pill"><i class="bi bi-eye"></i> Ver</a>
                                <a href="{{ route('receta.edit', $receta->id) }}" class="btn btn-sm btn-outline-primary w-50 rounded-pill"><i class="bi bi-pencil"></i> Editar</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                    <h5 class="text-muted fw-bold">No has publicado nada todavía</h5>
                    <a href="{{ route('receta.create') }}" class="btn btn-success mt-3 rounded-pill px-4">¡Publicar mi primera receta!</a>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        var triggerTabList = [].slice.call(document.querySelectorAll('#perfilTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabCompiler = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabCompiler.show()
                
                document.querySelectorAll('#perfilTabs button').forEach(btn => {
                    btn.style.backgroundColor = '';
                    btn.classList.remove('text-white');
                    btn.classList.add('text-secondary', 'bg-white');
                });
                this.style.backgroundColor = '#729c48';
                this.classList.remove('text-secondary', 'bg-white');
                this.classList.add('text-white');
            })
        })
    });
</script>
@endsection