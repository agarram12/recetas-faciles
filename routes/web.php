<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecetaController;

Route::get('/', [RecetaController::class, 'index']);

Route::get('/receta/{id}', [RecetaController::class, 'show'])->name('receta.show');