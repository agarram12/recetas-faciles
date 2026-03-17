@extends('layouts.app')

@section('content')
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                <img src="{{ asset($receta->url_imagen) }}" class="card-img-top" style="max-height: 400px; object-fit: cover;">

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="fw-bold mb-0" style="color: #333;">{{ $receta->titulo }}</h2>
                        <span class="badge px-3 py-2 rounded-pill" style="background-color: #729c48; color: white;">{{ $receta->categoria }}</span>
                    </div>

                    <p class="text-muted"><i class="bi bi-person-circle"></i> Por <strong>{{ $receta->autor }}</strong></p>

                    <div class="d-flex gap-4 mb-4 bg-light p-3 rounded" style="border-radius: 12px !important;">
                        <div class="fw-bold text-secondary"><i class="bi bi-clock text-success"></i> {{ $receta->tiempo_coccion }} min</div>
                        <div class="fw-bold text-secondary"><i class="bi bi-bar-chart text-success"></i> {{ $receta->dificultad }}</div>
                    </div>

                    @if($receta->descripcion)
                    <div class="mb-4">
                        <p class="fs-5 italic text-secondary" style="font-style: italic;">"{{ $receta->descripcion }}"</p>
                    </div>
                    @endif

                    <h5 class="fw-bold mb-3"><i class="bi bi-list-ol text-success"></i> Pasos de preparación</h5>

                    @php
                    // Separamos los pasos por el punto
                    $lista_pasos = array_filter(explode('.', $receta->pasos));
                    @endphp

                    <ul class="list-group list-group-flush mb-4">
                        @foreach($lista_pasos as $index => $paso)
                        @if(trim($paso) != '')
                        <li class="list-group-item bg-light border-0 mb-2 p-3 rounded shadow-sm d-flex align-items-start" style="border-radius: 12px !important;">
                            <span class="badge fs-6 me-3 rounded-circle d-flex align-items-center justify-content-center" style="background-color: #729c48; width: 30px; height: 30px;">{{ $index + 1 }}</span>
                            <span class="fs-6 text-dark">{{ trim($paso) }}.</span>
                        </li>
                        @endif
                        @endforeach
                    </ul>

                    <div class="mt-4 text-end">
                        <a href="/" class="btn btn-outline-secondary btn-sm rounded-pill px-4"><i class="bi bi-arrow-left"></i> Volver al Inicio</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <hr class="my-5" style="border-color: #e9ecef;">

    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <h4 class="fw-bold mb-4" style="color: #729c48;">
                <i class="bi bi-chat-dots"></i> Comentarios ({{ count($comentarios) }})
            </h4>

            <div class="card border-0 shadow-sm mb-5" style="border-radius: 16px; background-color: #f8f9fa;">
                <div class="card-body p-4">
                    <form action="{{ route('comentario.store', $receta->id) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-3">
                            <img src="{{ asset($comentario->avatar) }}"
                                class="rounded-circle shadow-sm"
                                width="50"
                                height="50"
                                style="object-fit: cover;"
                                alt="Avatar">

                            <div class="w-100">
                                <textarea class="form-control border-0 mb-2 p-3 shadow-sm" name="contenido" rows="2" placeholder="¿Qué te ha parecido esta receta? Deja tu comentario..." required style="border-radius: 12px; resize: none;"></textarea>

                                @error('contenido')
                                <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small>
                                @enderror

                                <div class="text-end mt-2">
                                    <button type="submit" class="btn text-white px-4 fw-bold shadow-sm" style="background-color: #729c48; border-radius: 25px;">
                                        Publicar comentario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="comentarios-lista">
                @forelse($comentarios as $comentario)
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3">
                            <div class="avatar-container shadow-sm">
                                <img src="{{ asset($comentario->avatar) }}"
                                    class="avatar-img"
                                    alt="Avatar">
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $comentario->nombre_usuario }}</h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        <i class="bi bi-clock"></i> {{ date('d/m/Y H:i', strtotime($comentario->created_at)) }}
                                    </small>
                                </div>
                                <p class="mb-0 mt-2 text-secondary" style="font-size: 0.95rem; line-height: 1.5;">
                                    {{ $comentario->contenido }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5 bg-light rounded-4 border-0 shadow-sm" style="border-radius: 16px !important;">
                    <i class="bi bi-chat-square-heart fs-1" style="color: #cbd5c0;"></i>
                    <h6 class="mt-3 fw-bold">Aún no hay comentarios</h6>
                    <p class="mb-0 small">¡Sé el primero en probar la receta y dar tu opinión!</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</main>
@endsection