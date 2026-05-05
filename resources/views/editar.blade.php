@extends('layouts.app')
@section('title', 'Editar Receta')

@section('content')
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h3 class="titulo-verde"><i class="bi bi-pencil-square"></i> Editar receta</h3>
                    </div>

                    @if ($errors->any())
                    <div class="alert alert-danger" style="border-radius: 12px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('receta.update', $receta->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- RF-91: Imagen principal con preview de la actual --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">FOTO DEL PLATO</label>
                            <div id="preview-principal-container" class="mb-2 text-center">
                                <img id="preview-principal" src="{{ asset($receta->url_imagen) }}" alt="Imagen actual" class="rounded shadow-sm" style="max-height: 220px; max-width: 100%; object-fit: cover;">
                            </div>
                            <input type="file" class="form-control" name="url_imagen" accept="image/jpeg,image/png,image/jpg,image/webp" id="input-imagen-principal">
                            <small class="text-muted">Déjalo en blanco para mantener la imagen actual. Formatos: JPEG, PNG, JPG, WebP. Máximo 2MB.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">TÍTULO DE LA RECETA</label>
                            <input type="text" class="form-control" name="titulo" value="{{ $receta->titulo }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">BREVE DESCRIPCIÓN</label>
                            <textarea class="form-control" name="descripcion" rows="2" required>{{ $receta->descripcion }}</textarea>
                        </div>

                        {{-- RF-95: Pasos con imágenes existentes y opción de cambiarlas --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">PASOS DE PREPARACIÓN</label>
                            <div id="pasos-container">
                                @php
                                // Separar los pasos por puntos
                                $pasos_array = array_filter(explode('. ', $receta->pasos));
                                $imagenes_existentes = $receta->imagenes_pasos ?? [];
                                @endphp

                                @foreach($pasos_array as $index => $paso)
                                <div class="paso-item card border-0 bg-light mb-3 p-3" style="border-radius: 12px;">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="titulo-verde fs-5 mt-2 paso-numero fw-bold">{{ $index + 1 }}.</div>
                                        <div class="flex-grow-1">
                                            <textarea class="form-control border-0 shadow-sm" name="pasos[]" rows="2" required>{{ str_replace('.', '', $paso) }}</textarea>
                                            <div class="mt-2">
                                                <label class="form-label small text-muted mb-1">
                                                    <i class="bi bi-camera"></i> Imagen del paso (opcional)
                                                </label>
                                                @if(isset($imagenes_existentes[$index]) && $imagenes_existentes[$index])
                                                <div class="preview-paso mb-2 text-center">
                                                    <img src="{{ asset($imagenes_existentes[$index]) }}" alt="Imagen paso {{ $index + 1 }}" class="rounded shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                                                    <div class="small text-muted mt-1">Imagen actual del paso {{ $index + 1 }}</div>
                                                </div>
                                                @else
                                                <div class="preview-paso mt-2 text-center d-none">
                                                    <img src="" alt="Preview paso" class="rounded shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: cover;">
                                                </div>
                                                @endif
                                                <input type="file" class="form-control form-control-sm input-imagen-paso" name="imagenes_pasos[{{ $index }}]" accept="image/jpeg,image/png,image/jpg,image/webp">
                                            </div>
                                        </div>
                                        @if($index > 0)
                                        <button type="button" class="btn btn-danger delete-paso px-3 mt-2" style="border-radius: 8px;"><i class="bi bi-trash3"></i></button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-paso" class="btn btn-outline-gris btn-sm mt-2">+ Añadir otro paso</button>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label text-secondary fw-bold">CATEGORÍA</label>
                                <select class="form-select border-0 bg-light" name="categoria_id" required>
                                    <option value="" disabled>Selecciona una categoría...</option>
                                    @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $cat->id == $receta->categoria_id ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">TIEMPO (MIN)</label>
                                <input type="number" class="form-control" name="tiempo_coccion" value="{{ $receta->tiempo_coccion }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">DIFICULTAD</label>
                                <select class="form-select" name="dificultad">
                                    <option value="Fácil" {{ $receta->dificultad == 'Fácil' ? 'selected' : '' }}>Fácil</option>
                                    <option value="Media" {{ $receta->dificultad == 'Media' ? 'selected' : '' }}>Media</option>
                                    <option value="Difícil" {{ $receta->dificultad == 'Difícil' ? 'selected' : '' }}>Difícil</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="/" class="btn btn-outline-gris">Cancelar</a>
                            <button type="submit" class="btn btn-verde">Actualizar Receta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pasosContainer = document.getElementById('pasos-container');
        const addPasoBtn = document.getElementById('add-paso');
        let contadorPasos = document.querySelectorAll('.paso-item').length;

        // Preview imagen principal
        const inputPrincipal = document.getElementById('input-imagen-principal');
        const previewImg = document.getElementById('preview-principal');

        inputPrincipal.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Preview imágenes de pasos
        pasosContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('input-imagen-paso')) {
                const wrapper = e.target.closest('.mt-2');
                let previewDiv = wrapper.querySelector('.preview-paso');
                let previewImg = previewDiv ? previewDiv.querySelector('img') : null;

                if (!previewDiv) {
                    previewDiv = document.createElement('div');
                    previewDiv.className = 'preview-paso mt-2 text-center';
                    previewDiv.innerHTML = '<img src="" alt="Preview paso" class="rounded shadow-sm" style="max-height: 120px; max-width: 100%; object-fit: cover;">';
                    wrapper.insertBefore(previewDiv, e.target);
                    previewImg = previewDiv.querySelector('img');
                }

                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        previewImg.src = ev.target.result;
                        previewDiv.classList.remove('d-none');
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            }
        });

        // Añadir paso
        addPasoBtn.addEventListener('click', function() {
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
                    <button type="button" class="btn btn-danger delete-paso px-3 mt-2" style="border-radius: 8px;"><i class="bi bi-trash3"></i></button>
                </div>
            `;
            pasosContainer.appendChild(nuevoPaso);
        });

        // Borrar paso
        pasosContainer.addEventListener('click', function(e) {
            const btnBorrar = e.target.closest('.delete-paso');
            if (btnBorrar) {
                btnBorrar.closest('.paso-item').remove();
                recalcularNumeros();
            }
        });

        function recalcularNumeros() {
            let num = 0;
            pasosContainer.querySelectorAll('.paso-item').forEach(function(paso, index) {
                num++;
                paso.querySelector('.paso-numero').textContent = num + '.';
                const inputImg = paso.querySelector('.input-imagen-paso');
                if (inputImg) {
                    inputImg.name = 'imagenes_pasos[' + index + ']';
                }
            });
            contadorPasos = num;
        }
    });
</script>
@endsection