@extends('layouts.app')

@section('title', 'Publicar Receta')

@section('content')
<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                    <h4 class="fw-bold text-success"><i class="bi bi-journal-plus"></i> Comparte tu receta</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('receta.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">Foto del plato</label>
                            <input type="file" class="form-control bg-light border-0" name="url_imagen" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted small text-uppercase">Título de la receta</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" name="titulo" placeholder="Ej: Macarrones de la abuela" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">Pasos de preparación (Separa cada paso con un punto)</label>
                            <textarea class="form-control bg-light border-0" name="pasos" rows="5" placeholder="1. Hierve el agua. 2. Echa la sal..." required></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted small text-uppercase">Categoría</label>
                                <select class="form-select bg-light border-0" name="categoria_id">
                                    <option value="1">Tradicional</option>
                                    <option value="2">Postres</option>
                                    <option value="3">Vegano</option>
                                    <option value="4">Carnes</option>
                                    <option value="5">Sopas y Cremas</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small text-uppercase">Tiempo (min)</label>
                                <input type="number" class="form-control bg-light border-0" name="tiempo_coccion" placeholder="45" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small text-uppercase">Dificultad</label>
                                <select class="form-select bg-light border-0" name="dificultad">
                                    <option value="Fácil">Fácil</option>
                                    <option value="Media" selected>Media</option>
                                    <option value="Difícil">Difícil</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="/" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-success rounded-pill px-5 text-white fw-bold">Publicar Receta</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection