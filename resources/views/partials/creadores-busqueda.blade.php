{{-- Partial: Creadores coincidentes en la búsqueda --}}
<div class="card mb-3 border-0 shadow-sm bg-white" style="border-radius: 16px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <i class="bi bi-people text-success fs-6 me-1"></i> Creadores encontrados
        </h6>
        <div class="row g-3">
            @foreach($usuarios as $usuario)
                <div class="col-md-4 col-sm-6 col-12">
                    <a href="{{ route('usuario.show', $usuario->id) }}" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none border hover-bg-light" style="background-color: #f8f9fa; border-color: #f1f3f5 !important; transition: all 0.2s ease;">
                        <img src="{{ asset($usuario->avatar) }}" class="rounded-circle shadow-sm border border-2 border-white" width="44" height="44" style="object-fit: cover;">
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $usuario->name }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $usuario->recetas_count }} {{ $usuario->recetas_count === 1 ? 'receta' : 'recetas' }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
