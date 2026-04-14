@extends('layouts.app')

@section('content')
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex align-items-center">
                    <a href="{{ route('usuario.show', $usuario->id) }}" class="btn btn-link text-decoration-none p-0 me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h5 class="mb-0">Siguiendo a {{ $usuario->name }}</h5>
                </div>
                <div class="card-body">
                    @forelse($seguidos as $seguido)
                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 border rounded">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($seguido->avatar ?? 'assets/img/logo.png') }}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">
                                        <a href="{{ route('usuario.show', $seguido->id) }}" class="text-decoration-none text-dark">
                                            {{ $seguido->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $seguido->email }}</small>
                                </div>
                            </div>
                            @auth
                                @if(Auth::id() !== $seguido->id)
                                    @php
                                        $sigueAEste = auth()->user()->sigueA($seguido);
                                    @endphp
                                    <form action="{{ route('usuario.follow', $seguido->id) }}" method="POST" class="ms-3">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $sigueAEste ? 'btn-outline-secondary' : 'btn-success' }} rounded-pill px-3">
                                            {{ $sigueAEste ? 'Siguiendo' : 'Seguir' }}
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-people display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">Este usuario no sigue a nadie todavía</h5>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-white border-0">
                    {{ $seguidos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection