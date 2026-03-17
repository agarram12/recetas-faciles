<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecetaController;
// feed principal
Route::get('/', [RecetaController::class, 'index']);
// Crear una receta
Route::get('/crear', [RecetaController::class, 'create'])->name('receta.create');
Route::post('/crear', [RecetaController::class, 'store'])->name('receta.store');
// Borrar una receta
Route::delete('/receta/{id}', [RecetaController::class, 'destroy'])->name('receta.destroy');
// Detalle de las recetas (dejar siempre al final)
Route::get('/receta/{id}', [RecetaController::class, 'show'])->name('receta.show');