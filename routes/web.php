<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecetaController;

Route::get('/', [RecetaController::class, 'index']);