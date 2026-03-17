<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecetaController;
// feed principal
Route::get('/', [RecetaController::class, 'index']);
// Crear una receta
Route::get('/crear', [RecetaController::class, 'create'])->name('receta.create');
Route::post('/crear', [RecetaController::class, 'store'])->name('receta.store');
// Editar una receta
Route::get('/receta/{id}/editar', [RecetaController::class, 'edit'])->name('receta.edit');
Route::put('/receta/{id}', [RecetaController::class, 'update'])->name('receta.update');
// Borrar una receta
Route::delete('/receta/{id}', [RecetaController::class, 'destroy'])->name('receta.destroy');
// Añadir comentarios a una receta
Route::post('/receta/{id}/comentar', [RecetaController::class, 'comentar'])->name('comentario.store');
// Detalle de las recetas (dejar siempre al final)
Route::get('/receta/{id}', [RecetaController::class, 'show'])->name('receta.show');