<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    // Mostrar el feed
    public function index()
    {
        $recetas = DB::table('recetas')
            ->join('usuarios', 'recetas.usuario_id', '=', 'usuarios.id')
            ->select('recetas.*', 'usuarios.nombre_usuario as autor', 'usuarios.avatar')
            ->orderBy('recetas.id', 'desc')
            ->get();

        $usuario_actual = DB::table('usuarios')->where('id', 1)->first();

        return view('index', [
            'recetas' => $recetas,
            'usuario_actual' => $usuario_actual
        ]);
    }

    // Formulario para crear una receta
    public function create()
    {
        return view('crear');
    }

    // Guardar la imagen de la receta
    public function store(Request $request)
    {
        $ruta_imagen_bd = 'assets/img/logo.png'; // Imagen por defecto

        if ($request->hasFile('url_imagen')) {
            $file = $request->file('url_imagen');
            $nombre_archivo = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img'), $nombre_archivo);
            $ruta_imagen_bd = 'assets/img/' . $nombre_archivo;
        }

        $pasos_array = $request->pasos; 
        $pasos_texto_unificado = implode('. ', array_filter($pasos_array)) . '.';

        // Insertar en la BD
        DB::table('recetas')->insert([
            'usuario_id' => 1,
            'categoria_id' => $request->categoria_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'pasos' => $pasos_texto_unificado, 
            'url_imagen' => $ruta_imagen_bd,
            'tiempo_coccion' => $request->tiempo_coccion,
            'dificultad' => $request->dificultad
        ]);

        return redirect('/');
    }

    // Mostrar los detalles de una receta
    public function show($id)
    {
        $receta = DB::table('recetas')
            ->join('usuarios', 'recetas.usuario_id', '=', 'usuarios.id')
            ->join('categorias', 'recetas.categoria_id', '=', 'categorias.id')
            ->select('recetas.*', 'usuarios.nombre_usuario as autor', 'usuarios.avatar', 'categorias.nombre as categoria')
            ->where('recetas.id', $id)
            ->first();

        if (!$receta) {
            abort(404);
        }

        return view('detalle', ['receta' => $receta]);
    }

    public function destroy($id)
    {
        // Se busca por ID y se borra
        DB::table('recetas')->where('id', $id)->delete();
        // volver a inicio
        return redirect('/');
    }
}
