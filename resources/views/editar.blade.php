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
                        @method('PUT') <div class="mb-4">
                            <label class="form-label">FOTO DEL PLATO (Déjalo en blanco para mantener la actual)</label>
                            <input type="file" class="form-control" name="url_imagen" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">TÍTULO DE LA RECETA</label>
                            <input type="text" class="form-control" name="titulo" value="{{ $receta->titulo }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">BREVE DESCRIPCIÓN</label>
                            <textarea class="form-control" name="descripcion" rows="2" required>{{ $receta->descripcion }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">PASOS DE PREPARACIÓN</label>
                            <div id="pasos-container">
                                @php
                                // Separamos los pasos por puntos
                                $pasos_array = array_filter(explode('. ', $receta->pasos));
                                @endphp

                                @foreach($pasos_array as $index => $paso)
                                <div class="paso-item d-flex gap-3 mb-3">
                                    <div class="titulo-verde fs-5 mt-2 paso-numero">{{ $index + 1 }}.</div>
                                    <textarea class="form-control" name="pasos[]" rows="2" required>{{ str_replace('.', '', $paso) }}</textarea>
                                    @if($index > 0)
                                    <button type="button" class="btn btn-danger delete-paso px-3" style="border-radius: 8px;"><i class="bi bi-trash3"></i></button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-paso" class="btn btn-outline-gris btn-sm mt-2">+ Añadir otro paso</button>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label">CATEGORÍA</label>
                                <select class="form-select" name="categoria_id">
                                    <option value="1" {{ $receta->categoria_id == 1 ? 'selected' : '' }}>Tradicional</option>
                                    <option value="2" {{ $receta->categoria_id == 2 ? 'selected' : '' }}>Postres</option>
                                    <option value="3" {{ $receta->categoria_id == 3 ? 'selected' : '' }}>Vegano</option>
                                    <option value="4" {{ $receta->categoria_id == 4 ? 'selected' : '' }}>Carnes</option>
                                    <option value="5" {{ $receta->categoria_id == 5 ? 'selected' : '' }}>Sopas y Cremas</option>
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
        addPasoBtn.addEventListener('click', function() {
            contadorPasos++;
            const nuevoPaso = document.createElement('div');
            nuevoPaso.className = 'paso-item d-flex gap-3 mb-3';
            nuevoPaso.innerHTML = '<div class="titulo-verde fs-5 mt-2 paso-numero">' + contadorPasos + '.</div>' +
                '<textarea class="form-control" name="pasos[]" rows="2" placeholder="Escribe el paso ' + contadorPasos + '..." required></textarea>' +
                '<button type="button" class="btn btn-danger delete-paso px-3" style="border-radius: 8px;"><i class="bi bi-trash3"></i></button>';

            pasosContainer.appendChild(nuevoPaso);
        });

        pasosContainer.addEventListener('click', function(e) {
            const btnBorrar = e.target.closest('.delete-paso');
            if (btnBorrar) {
                btnBorrar.closest('.paso-item').remove();
                recalcularNumeros();
            }
        });

        function recalcularNumeros() {
            let num = 0;
            pasosContainer.querySelectorAll('.paso-item').forEach(function(paso) {
                num++;
                paso.querySelector('.paso-numero').textContent = num + '.';
            });
            contadorPasos = num;
        }
    });
</script>
@endsection