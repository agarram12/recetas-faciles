<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/receta/crear', [App\Http\Controllers\RecetaController::class, 'create'])->name('receta.create');
Route::post('/receta', [App\Http\Controllers\RecetaController::class, 'store'])->name('receta.store');
Route::get('/receta/{id}', [App\Http\Controllers\RecetaController::class, 'show'])->name('receta.show');
Route::get('/receta/{id}/editar', [App\Http\Controllers\RecetaController::class, 'edit'])->name('receta.edit');
Route::put('/receta/{id}', [App\Http\Controllers\RecetaController::class, 'update'])->name('receta.update');
Route::delete('/receta/{id}', [App\Http\Controllers\RecetaController::class, 'destroy'])->name('receta.destroy');
Route::post('/receta/{id}/valorar', [App\Http\Controllers\InteraccionController::class, 'valorar'])->name('receta.valorar')->middleware('auth');
Route::post('/receta/{id}/comentario', [App\Http\Controllers\InteraccionController::class, 'comentar'])->name('comentario.store')->middleware('auth');
Route::post('/receta/{receta}/favorito', [App\Http\Controllers\InteraccionController::class, 'toggleFavorito'])->name('receta.favorito')->middleware('auth');


Route::get('/', [App\Http\Controllers\RecetaController::class, 'index'])->name('inicio');
Route::get('/', [App\Http\Controllers\RecetaController::class, 'index'])->name('inicio');