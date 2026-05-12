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
                        <div class="d-flex gap-2">
                            {{-- RF-102: Favorito AJAX sin recarga --}}
                            @auth
                                @php
                                    $esFavorito = Auth::user()->recetasFavoritas->contains($receta->id);
                                @endphp
                                <button type="button" 
                                        class="btn btn-sm {{ $esFavorito ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3 shadow-sm btn-favorito-detalle"
                                        data-url="{{ route('receta.favorito', $receta->id) }}"
                                        id="btnFavoritoDetalle">
                                    <i class="bi {{ $esFavorito ? 'bi-heart-fill' : 'bi-heart' }}"></i> 
                                    <span>{{ $esFavorito ? 'Guardada' : 'Guardar' }}</span>
                                </button>
                            @endauth

                            <span class="badge px-3 py-2 rounded-pill d-flex align-items-center" style="background-color: #729c48; color: white;">{{ $receta->categoria->nombre ?? 'Sin categoría' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2 mb-3">
                        {{-- Estrellas de la media --}}
                        <div class="text-warning fs-5" id="estrellasMedia">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= round($media ?? 0) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <span class="text-muted fw-bold" id="mediaTexto">({{ number_format($media ?? 0, 1) }})</span>

                        {{-- RF-102: Valoración AJAX --}}
                        @auth
                        <form id="formValorar" data-ajax="true" class="ms-3 d-flex align-items-center" data-url="{{ route('receta.valorar', $receta->id) }}">
                            @csrf
                            <select name="puntuacion" id="selectPuntuacion" class="form-select form-select-sm border-0 bg-light me-2" style="width: auto; cursor: pointer;" required>
                                <option value="" disabled selected>Puntuar...</option>
                                <option value="5">5⭐ Increíble</option>
                                <option value="4">4⭐ Muy buena</option>
                                <option value="3">3⭐ Normal</option>
                                <option value="2">2⭐ Regular</option>
                                <option value="1">1⭐ Mala</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" id="btnValorar">
                                <span class="btn-text-valorar">Votar</span>
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerValorar" role="status"></span>
                            </button>
                        </form>
                        @endauth
                    </div>
                    <p class="text-muted"><i class="bi bi-person-circle"></i> Por <strong>{{ $receta->autor->name ?? 'Anónimo' }}</strong></p>
                    <div class="d-flex gap-4 mb-4 bg-light p-3 rounded" style="border-radius: 12px !important;">
                        <div class="fw-bold text-secondary"><i class="bi bi-clock text-success"></i> {{ $receta->tiempo_coccion }} min</div>
                        <div class="fw-bold text-secondary"><i class="bi bi-bar-chart text-success"></i> {{ $receta->dificultad }}</div>
                    </div>
                    @if($receta->descripcion)
                    <div class="mb-4">
                        <p class="fs-5 italic text-secondary" style="font-style: italic;">"{{ $receta->descripcion }}"</p>
                    </div>
                    @endif

                    {{-- RF-94: Pasos de preparación con imágenes --}}
                    <h5 class="fw-bold mb-3"><i class="bi bi-list-ol text-success"></i> Pasos de preparación</h5>
                    @php
                    $lista_pasos = array_filter(explode('.', $receta->pasos));
                    $imagenes_pasos = $receta->imagenes_pasos ?? [];
                    @endphp

                    <ul class="list-group list-group-flush mb-4">
                        @foreach($lista_pasos as $index => $paso)
                        @if(trim($paso) != '')
                        <li class="list-group-item bg-light border-0 mb-2 p-3 rounded shadow-sm" style="border-radius: 12px !important;">
                            <div class="d-flex align-items-start">
                                <span class="badge fs-6 me-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: #729c48; width: 30px; height: 30px;">{{ $index + 1 }}</span>
                                <div class="flex-grow-1">
                                    <span class="fs-6 text-dark">{{ trim($paso) }}.</span>
                                    {{-- Mostrar imagen del paso si existe --}}
                                    @if(isset($imagenes_pasos[$index]) && $imagenes_pasos[$index])
                                    <div class="mt-2">
                                        <img src="{{ asset($imagenes_pasos[$index]) }}" 
                                             alt="Imagen paso {{ $index + 1 }}" 
                                             class="rounded shadow-sm" 
                                             style="max-height: 200px; max-width: 100%; object-fit: cover; cursor: pointer;"
                                             onclick="this.classList.toggle('img-ampliada')"
                                        >
                                    </div>
                                    @endif
                                </div>
                            </div>
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
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4" style="color: #729c48;">
                <i class="bi bi-chat-dots"></i> Comentarios (<span id="contadorComentarios">{{ count($comentarios) }}</span>)
            </h4>

            <div class="card border-0 shadow-sm mb-5" style="border-radius: 16px; background-color: #f8f9fa;">
                <div class="card-body p-3">
                    {{-- RF-102: Comentario AJAX --}}
                    @auth
                    <form id="formComentar" data-ajax="true" data-url="{{ route('comentario.store', $receta->id) }}">
                        @csrf
                        <div class="d-flex gap-3">
                            <img src="{{ asset(auth()->user()->avatar ?? 'assets/img/logo.png') }}"
                                class="rounded-circle shadow-sm"
                                width="50"
                                height="50"
                                style="object-fit: cover;"
                                alt="Tu Avatar"
                                onerror="this.src='{{ asset('assets/img/logo.png') }}'">

                            <div class="w-100">
                                <textarea class="form-control border-0 mb-2 p-3 shadow-sm" name="contenido" id="inputComentario" rows="2" placeholder="¿Qué te ha parecido esta receta? Deja tu comentario..." required style="border-radius: 12px; resize: none;" maxlength="500"></textarea>

                                @error('contenido')
                                <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small>
                                @enderror

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted"><span id="charCount">0</span>/500</small>
                                    <button type="submit" class="btn text-white px-4 fw-bold shadow-sm" style="background-color: #729c48; border-radius: 25px;" id="btnComentar">
                                        <span class="btn-text-comentar">Publicar comentario</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinnerComentar" role="status"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endauth

                    @guest
                    <div class="text-center py-4">
                        <h6 class="fw-bold text-secondary mb-3">¿Quieres dar tu opinión sobre esta receta?</h6>
                        <a href="{{ route('login') }}" class="btn text-white px-4 rounded-pill shadow-sm" style="background-color: #729c48;">
                            <i class="bi bi-box-arrow-in-right"></i> Inicia sesión para comentar
                        </a>
                        <p class="mt-2 mb-0 small text-muted">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-success text-decoration-none fw-bold">Regístrate gratis</a></p>
                    </div>
                    @endguest

                </div>
            </div>

            <div class="comentarios-lista" id="listaComentarios">
                @forelse($comentarios as $comentario)
                <div class="card border-0 shadow-sm mb-3 comentario-item" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3">
                            <div class="avatar-container shadow-sm">
                                <img src="{{ asset($comentario->autor->avatar ?? 'assets/img/logo.png') }}"
                                    class="avatar-img"
                                    alt="Avatar">
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $comentario->autor->name ?? 'Usuario' }}</h6>
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
                <div class="text-center text-muted py-5 bg-light rounded-4 border-0 shadow-sm" style="border-radius: 16px !important;" id="sinComentarios">
                    <i class="bi bi-chat-square-heart fs-1" style="color: #cbd5c0;"></i>
                    <h6 class="mt-3 fw-bold">Aún no hay comentarios</h6>
                    <p class="mb-0 small">¡Sé el primero en probar la receta y dar tu opinión!</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</main>

<style>
    .img-ampliada {
        max-height: 500px !important;
        transition: max-height 0.3s ease;
    }
    /* RF-99: Animación de entrada para comentarios nuevos */
    .comentario-nuevo {
        animation: slideInComment 0.4s ease forwards;
    }
    @keyframes slideInComment {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ============================================
    // RF-102: COMENTARIOS AJAX (sin recarga)
    // ============================================
    const formComentar = document.getElementById('formComentar');
    if (formComentar) {
        const inputComentario = document.getElementById('inputComentario');
        const charCount = document.getElementById('charCount');

        // Contador de caracteres
        inputComentario.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        formComentar.addEventListener('submit', function(e) {
            e.preventDefault();

            const contenido = inputComentario.value.trim();
            if (!contenido) return;

            const btnComentar = document.getElementById('btnComentar');
            const spinnerComentar = document.getElementById('spinnerComentar');
            const btnTextComentar = formComentar.querySelector('.btn-text-comentar');

            // RF-100: Mostrar loading en botón
            btnComentar.disabled = true;
            btnTextComentar.classList.add('d-none');
            spinnerComentar.classList.remove('d-none');

            fetch(formComentar.dataset.url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ contenido: contenido })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Eliminar el mensaje "Aún no hay comentarios"
                    const sinComentarios = document.getElementById('sinComentarios');
                    if (sinComentarios) sinComentarios.remove();

                    // Insertar el nuevo comentario al principio de la lista
                    const lista = document.getElementById('listaComentarios');
                    const nuevoHtml = `
                        <div class="card border-0 shadow-sm mb-3 comentario-item comentario-nuevo" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="d-flex gap-3">
                                    <div class="avatar-container shadow-sm">
                                        <img src="${data.avatar}" class="avatar-img" alt="Avatar">
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="fw-bold mb-0 text-dark">${data.autor}</h6>
                                            <small class="text-muted" style="font-size: 0.8rem;">
                                                <i class="bi bi-clock"></i> ${data.fecha}
                                            </small>
                                        </div>
                                        <p class="mb-0 mt-2 text-secondary" style="font-size: 0.95rem; line-height: 1.5;">
                                            ${data.contenido}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    lista.insertAdjacentHTML('afterbegin', nuevoHtml);

                    // Actualizar contador
                    const contador = document.getElementById('contadorComentarios');
                    if (contador) contador.textContent = parseInt(contador.textContent) + 1;

                    // Limpiar formulario
                    inputComentario.value = '';
                    charCount.textContent = '0';

                    mostrarToast(data.mensaje, 'success');
                } else if (data.errors) {
                    const msgs = Object.values(data.errors).flat().join(', ');
                    mostrarToast(msgs, 'danger');
                }
            })
            .catch(err => {
                console.error('Error comentario:', err);
                mostrarToast('Error al publicar el comentario', 'danger');
            })
            .finally(function() {
                btnComentar.disabled = false;
                btnTextComentar.classList.remove('d-none');
                spinnerComentar.classList.add('d-none');
            });
        });
    }

    // ============================================
    // RF-102: VALORACIÓN AJAX (sin recarga)
    // ============================================
    const formValorar = document.getElementById('formValorar');
    if (formValorar) {
        formValorar.addEventListener('submit', function(e) {
            e.preventDefault();

            const select = document.getElementById('selectPuntuacion');
            const puntuacion = select.value;
            if (!puntuacion) {
                mostrarToast('Selecciona una puntuación', 'warning');
                return;
            }

            const btnValorar = document.getElementById('btnValorar');
            const spinnerValorar = document.getElementById('spinnerValorar');
            const btnTextValorar = formValorar.querySelector('.btn-text-valorar');

            // RF-100: Loading
            btnValorar.disabled = true;
            btnTextValorar.classList.add('d-none');
            spinnerValorar.classList.remove('d-none');

            fetch(formValorar.dataset.url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ puntuacion: parseInt(puntuacion) })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Actualizar estrellas
                    const estrellasContainer = document.getElementById('estrellasMedia');
                    const mediaTexto = document.getElementById('mediaTexto');
                    
                    if (estrellasContainer && data.media !== undefined) {
                        let estrellasHtml = '';
                        for (let i = 1; i <= 5; i++) {
                            estrellasHtml += '<i class="bi ' + (i <= Math.round(data.media) ? 'bi-star-fill' : 'bi-star') + '"></i>';
                        }
                        estrellasContainer.innerHTML = estrellasHtml;
                    }
                    if (mediaTexto && data.media !== undefined) {
                        mediaTexto.textContent = '(' + data.media.toFixed(1) + ')';
                    }

                    // Reset select
                    select.selectedIndex = 0;

                    mostrarToast(data.mensaje, 'success');
                }
            })
            .catch(err => {
                console.error('Error valoración:', err);
                mostrarToast('Error al enviar la valoración', 'danger');
            })
            .finally(function() {
                btnValorar.disabled = false;
                btnTextValorar.classList.remove('d-none');
                spinnerValorar.classList.add('d-none');
            });
        });
    }

    // ============================================
    // RF-102: FAVORITO AJAX en detalle (sin recarga)
    // ============================================
    const btnFavDetalle = document.getElementById('btnFavoritoDetalle');
    if (btnFavDetalle) {
        btnFavDetalle.addEventListener('click', function(e) {
            e.preventDefault();
            const boton = this;
            const url = boton.dataset.url;

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
                const icono = boton.querySelector('i');
                const texto = boton.querySelector('span');

                if (data.esFavorito) {
                    boton.classList.remove('btn-outline-danger');
                    boton.classList.add('btn-danger');
                    icono.classList.remove('bi-heart');
                    icono.classList.add('bi-heart-fill');
                    texto.textContent = 'Guardada';
                } else {
                    boton.classList.remove('btn-danger');
                    boton.classList.add('btn-outline-danger');
                    icono.classList.remove('bi-heart-fill');
                    icono.classList.add('bi-heart');
                    texto.textContent = 'Guardar';
                }

                mostrarToast(data.mensaje, 'success');
            })
            .catch(err => {
                console.error('Error favorito:', err);
                mostrarToast('Error al actualizar favoritos', 'danger');
            });
        });
    }
});
</script>
@endsection