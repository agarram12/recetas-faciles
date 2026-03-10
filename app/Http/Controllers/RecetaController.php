<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index()
    {
        $recetas = DB::table('recetas')
            ->join('usuarios','recetas.usuario_id', '=', 'usuarios.id')
            ->select('recetas.*', 'usuarios.nombre_usuario as autor', 'usuarios.avatar')
            ->orderBy('recetas.id', 'desc')
            ->get();
        return view('index', ['recetas' => $recetas]);
    }
}
