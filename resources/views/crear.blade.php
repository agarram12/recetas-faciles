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
                    
                    <form action="{{ route('receta.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">FOTO DEL PLATO</label>
                            <input type="file" class="form-control" name="url_imagen" accept="image/*" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">TÍTULO DE LA RECETA</label>
                            <input type="text" class="form-control" name="titulo" placeholder="Ej: Macarrones de la abuela" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">BREVE DESCRIPCIÓN</label>
                            <textarea class="form-control" name="descripcion" rows="2" placeholder="Ej: Un plato tradicional perfecto para los domingos en familia..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">PASOS DE PREPARACIÓN</label>
                            
                            <div id="pasos-container">
                                <div class="paso-item d-flex gap-3 mb-3">
                                    <div class="titulo-verde fs-5 mt-2 paso-numero">1.</div>
                                    <textarea class="form-control" name="pasos[]" rows="2" placeholder="Escribe el primer paso..." required></textarea>
                                </div>
                            </div>

                            <button type="button" id="add-paso" class="btn btn-outline-gris btn-sm mt-2">
                                + Añadir otro paso
                            </button>
                        </div>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label">CATEGORÍA</label>
                                <select class="form-select" name="categoria_id">
                                    <option value="1">Tradicional</option>
                                    <option value="2">Postres</option>
                                    <option value="3">Vegano</option>
                                    <option value="4">Carnes</option>
                                    <option value="5">Sopas y Cremas</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">TIEMPO (MIN)</label>
                                <input type="number" class="form-control" name="tiempo_coccion" placeholder="45" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">DIFICULTAD</label>
                                <select class="form-select" name="dificultad">
                                    <option value="Fácil">Fácil</option>
                                    <option value="Media" selected>Media</option>
                                    <option value="Difícil">Difícil</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 border-top pt-4">
                            <a href="/" class="btn btn-outline-gris">Cancelar</a>
                            <button type="submit" class="btn btn-verde">Publicar Receta</button>
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

        // Añadir paso
        addPasoBtn.addEventListener('click', function () {
            contadorPasos++;
            
            const nuevoPaso = document.createElement('div');
            nuevoPaso.className = 'paso-item d-flex gap-3 mb-3';
            nuevoPaso.innerHTML = `
                <div class="titulo-verde fs-5 mt-2 paso-numero">${contadorPasos}.</div>
                <textarea class="form-control" name="pasos[]" rows="2" placeholder="Escribe el paso ${contadorPasos}..." required></textarea>
                <button type="button" class="btn btn-danger delete-paso px-3" style="border-radius: 8px;">
                    <i class="bi bi-trash3"></i>
                </button>
            `;
            
            pasosContainer.appendChild(nuevoPaso);
        });

        // Borrar paso
        pasosContainer.addEventListener('click', function (e) {
            const btnBorrar = e.target.closest('.delete-paso');
            if (btnBorrar) {
                const pasoItem = btnBorrar.closest('.paso-item');
                pasoItem.remove();
                recalcularNumeros(); // Actualizar los números
            }
        });

        // Calcular números tras borrar
        function recalcularNumeros() {
            const todosLosPasos = pasosContainer.querySelectorAll('.paso-item');
            contadorPasos = 0;
            todosLosPasos.forEach((paso) => {
                contadorPasos++;
                paso.querySelector('.paso-numero').textContent = contadorPasos + '.';
                paso.querySelector('textarea').placeholder = 'Escribe el paso ' + contadorPasos + '...';
            });
        }
    });
</script>
@endsection