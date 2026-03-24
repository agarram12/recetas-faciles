<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Feed
Route::get('/', [App\Http\Controllers\RecetaController::class, 'index'])->name('inicio');

// Perfil
Route::get('/dashboard', [ProfileController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Rutas para el CRUD
Route::get('/receta/crear', [App\Http\Controllers\RecetaController::class, 'create'])->name('receta.create')->middleware('auth');
Route::post('/receta', [App\Http\Controllers\RecetaController::class, 'store'])->name('receta.store')->middleware('auth');
Route::get('/receta/{id}', [App\Http\Controllers\RecetaController::class, 'show'])->name('receta.show');
Route::get('/receta/{id}/editar', [App\Http\Controllers\RecetaController::class, 'edit'])->name('receta.edit')->middleware('auth');
Route::put('/receta/{id}', [App\Http\Controllers\RecetaController::class, 'update'])->name('receta.update')->middleware('auth');
Route::delete('/receta/{id}', [App\Http\Controllers\RecetaController::class, 'destroy'])->name('receta.destroy')->middleware('auth');

Route::post('/receta/{id}/valorar', [App\Http\Controllers\InteraccionController::class, 'valorar'])->name('receta.valorar')->middleware('auth');
Route::post('/receta/{id}/comentario', [App\Http\Controllers\InteraccionController::class, 'comentar'])->name('comentario.store')->middleware('auth');
Route::post('/receta/{receta}/favorito', [App\Http\Controllers\InteraccionController::class, 'toggleFavorito'])->name('receta.favorito')->middleware('auth');