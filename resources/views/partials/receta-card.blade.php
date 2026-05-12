{{-- Partial: Tarjeta de receta reutilizable (feed + AJAX infinite scroll) --}}
<div class="col-md-6 receta-card-item">
    <article class="card h-100 border-0 shadow-sm card-hover">
        
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
            <a href="{{ route('receta.show', $receta->id) }}">
                <img src="{{ asset($receta->url_imagen) }}" class="w-100" style="height: 200px; object-fit: cover;" alt="{{ $receta->titulo }}" loading="lazy">
            </a>
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
                        $esFavorito = in_array($receta->id, $favoritoIds ?? []);
                    @endphp
                    <button type="button" 
                            class="btn btn-sm rounded-pill w-50 btn-favorito {{ $esFavorito ? 'btn-danger text-white' : 'btn-outline-danger' }}"
                            data-receta-id="{{ $receta->id }}"
                            data-url="{{ route('receta.favorito', $receta->id) }}">
                        <i class="bi {{ $esFavorito ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm rounded-pill w-50">
                        <i class="bi bi-heart"></i>
                    </a>
                @endauth
            </div>
        </div>
    </article>
</div>
