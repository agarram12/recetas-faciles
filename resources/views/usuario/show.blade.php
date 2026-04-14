@extends('layouts.app')

@section('content')
<main class="container py-5">
    <div class="row gy-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center gap-4">
                    <img src="{{ asset($usuario->avatar ?? 'assets/img/logo.png') }}" class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                    <div>
                        <h2 class="fw-bold mb-1" style="color: #729c48;">{{ $usuario->name }}</h2>
                        <p class="text-muted mb-2">{{ $usuario->email }}</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('usuario.seguidores', $usuario->id) }}" class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 text-decoration-none">{{ $usuario->seguidores()->count() }} seguidores</a>
                            <a href="{{ route('usuario.seguidos', $usuario->id) }}" class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 text-decoration-none">{{ $usuario->seguidos()->count() }} seguidos</a>
                        </div>
                    </div>
                    <div class="ms-auto">
                        @auth
                            @if(Auth::id() !== $usuario->id)
                                <form action="{{ route('usuario.follow', $usuario->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $esSeguido ? 'outline-secondary' : 'success' }} rounded-pill px-4">
                                        <i class="bi {{ $esSeguido ? 'bi-person-dash' : 'bi-person-plus' }} me-1"></i>
                                        {{ $esSeguido ? 'Dejar de seguir' : 'Seguir' }}
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4">Recetas de {{ $usuario->name }}</h5>
                @forelse($recetas as $receta)
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-4">
                                <img src="{{ asset($receta->url_imagen) }}" class="img-fluid rounded-start" style="height: 150px; object-fit: cover; width: 100%;">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-1">{{ $receta->titulo }}</h6>
                                    <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($receta->descripcion, 100) }}</p>
                                    <a href="{{ route('receta.show', $receta->id) }}" class="btn btn-sm btn-outline-success rounded-pill">Ver receta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                        <h5 class="text-muted fw-bold">Este usuario aún no ha publicado recetas</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</main>
@endsection
