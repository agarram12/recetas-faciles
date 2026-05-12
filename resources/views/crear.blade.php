@extends('layouts.app')

@section('title', 'Publicar Receta')

@section('content')

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-5">
                        <h3 class="titulo-verde"><i class="bi bi-journal-plus"></i> Comparte tu receta</h3>
                    </div>
                    <!-- Mostrar errores de validación arriba del formulario -->
                    @if ($errors->any())
                        <div class="alert alert-danger" style="border-radius: 12px;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('receta.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- RF-91: Imagen principal con preview --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">FOTO DEL PLATO</label>
                            <div id="preview-principal-container" class="mb-2 text-center d-none">
                                <img id="preview-principal" src="" alt="Preview" class="rounded shadow-sm" style="max-height: 220px; max-width: 100%; object-fit: cover;">
                            </div>
                            <input type="file" class="form-control" name="url_imagen" accept="image/jpeg,image/png,image/jpg,image/webp" required id="input-imagen-principal">
                            <small class="text-muted">Formatos: JPEG, PNG, JPG, WebP. Máximo 2MB.</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">TÍTULO DE LA RECETA</label>
                            <input type="text" class="form-control" name="titulo" placeholder="Ej: Macarrones de la abuela" value="{{ old('titulo') }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">BREVE DESCRIPCIÓN</label>
                            <textarea class="form-control" name="descripcion" rows="2" placeholder="Ej: Un plato tradicional perfecto para los domingos en familia..." required>{{ old('descripcion') }}</textarea>
                        </div>

                        {{-- RF-95: Pasos con opción de imagen individual --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">PASOS DE PREPARACIÓN</label>
                            
                            <div id="pasos-container">
                                <div class="paso-item card border-0 bg-light mb-3 p-3" style="border-radius: 12px;">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="titulo-verde fs-5 mt-2 paso-numero fw-bold">1.</div>
                                        <div class="flex-grow-1">
                                            <textarea class="form-control border-0 shadow-sm" name="pasos[]" rows="2" placeholder="Escribe el primer paso..." required></textarea>
                                            <div class="mt-2">
                                                <label class="form-label small text-muted mb-1">
                                                    <i class="bi bi-camera"></i> Imagen del paso (opcional)
                                                </label>
                                                <input type="file" class="form-control form-control-sm input-imagen-paso" name="imagenes_pasos[0]" accept="image/jpeg,image/png,image/jpg,image/webp">
                                                <div class="preview-paso mt-2 text-center d-none">
                                                    <img src="" alt="Preview paso" class="rounded shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="add-paso" class="btn btn-outline-gris btn-sm mt-2">
                                + Añadir otro paso
                            </button>
                        </div>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label">CATEGORÍA</label>
                                <select class="form-select" name="categoria_id" required>
                                    <option value="" disabled selected>Selecciona una categoría...</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">TIEMPO (MIN)</label>
                                <input type="number" class="form-control" name="tiempo_coccion" placeholder="45" value="{{ old('tiempo_coccion') }}" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">DIFICULTAD</label>
                                <select class="form-select" name="dificultad">
                                    <option value="Fácil" {{ old('dificultad') == 'Fácil' ? 'selected' : '' }}>Fácil</option>
                                    <option value="Media" {{ old('dificultad', 'Media') == 'Media' ? 'selected' : '' }}>Media</option>
                                    <option value="Difícil" {{ old('dificultad') == 'Difícil' ? 'selected' : '' }}>Difícil</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="/" class="btn btn-outline-gris">Cancelar</a>
                            <button type="submit" class="btn btn-verde" id="btnPublicar">
                                <span class="btn-text-publicar"><i class="bi bi-upload me-1"></i> Publicar Receta</span>
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerPublicar" role="status"></span>
                                <span class="d-none" id="textoPublicando">Publicando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pasosContainer = document.getElementById('pasos-container');
        const addPasoBtn = document.getElementById('add-paso');
        let contadorPasos = 1;

        // RF-100: Loading state al enviar formulario
        const formCrear = document.querySelector('form[action="{{ route("receta.store") }}"]');
        if (formCrear) {
            formCrear.addEventListener('submit', function() {
                const btn = document.getElementById('btnPublicar');
                const spinner = document.getElementById('spinnerPublicar');
                const textoPublicando = document.getElementById('textoPublicando');
                const textoOriginal = document.querySelector('.btn-text-publicar');
                if (btn) {
                    btn.disabled = true;
                    textoOriginal.classList.add('d-none');
                    spinner.classList.remove('d-none');
                    textoPublicando.classList.remove('d-none');
                }
            });
        }

        // Preview imagen principal
        const inputPrincipal = document.getElementById('input-imagen-principal');
        const previewContainer = document.getElementById('preview-principal-container');
        const previewImg = document.getElementById('preview-principal');

        inputPrincipal.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.classList.add('d-none');
            }
        });

        // Preview imágenes de pasos (delegación de eventos)
        pasosContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('input-imagen-paso')) {
                const previewDiv = e.target.closest('.mt-2').querySelector('.preview-paso');
                const previewImg = previewDiv.querySelector('img');
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        previewImg.src = ev.target.result;
                        previewDiv.classList.remove('d-none');
                    };
                    reader.readAsDataURL(e.target.files[0]);
                } else {
                    previewDiv.classList.add('d-none');
                }
            }
        });

        // Añadir paso
        addPasoBtn.addEventListener('click', function () {
            contadorPasos++;
            
            const nuevoPaso = document.createElement('div');
            nuevoPaso.className = 'paso-item card border-0 bg-light mb-3 p-3';
            nuevoPaso.style.borderRadius = '12px';
            nuevoPaso.innerHTML = `
                <div class="d-flex gap-3 align-items-start">
                    <div class="titulo-verde fs-5 mt-2 paso-numero fw-bold">${contadorPasos}.</div>
                    <div class="flex-grow-1">
                        <textarea class="form-control border-0 shadow-sm" name="pasos[]" rows="2" placeholder="Escribe el paso ${contadorPasos}..." required></textarea>
                        <div class="mt-2">
                            <label class="form-label small text-muted mb-1">
                                <i class="bi bi-camera"></i> Imagen del paso (opcional)
                            </label>
                            <input type="file" class="form-control form-control-sm input-imagen-paso" name="imagenes_pasos[${contadorPasos - 1}]" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="preview-paso mt-2 text-center d-none">
                                <img src="" alt="Preview paso" class="rounded shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger delete-paso px-3 mt-2" style="border-radius: 8px;">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            `;
            
            pasosContainer.appendChild(nuevoPaso);
        });

        // Borrar paso
        pasosContainer.addEventListener('click', function (e) {
            const btnBorrar = e.target.closest('.delete-paso');
            if (btnBorrar) {
                const pasoItem = btnBorrar.closest('.paso-item');
                pasoItem.remove();
                recalcularNumeros();
            }
        });

        // Calcular números tras borrar
        function recalcularNumeros() {
            const todosLosPasos = pasosContainer.querySelectorAll('.paso-item');
            contadorPasos = 0;
            todosLosPasos.forEach((paso, index) => {
                contadorPasos++;
                paso.querySelector('.paso-numero').textContent = contadorPasos + '.';
                paso.querySelector('textarea').placeholder = 'Escribe el paso ' + contadorPasos + '...';
                // Actualizar el name del input de imagen para mantener índices correctos
                const inputImg = paso.querySelector('.input-imagen-paso');
                if (inputImg) {
                    inputImg.name = 'imagenes_pasos[' + index + ']';
                }
            });
        }
    });
</script>
@endsection