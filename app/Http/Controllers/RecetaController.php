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
            ->join('users', 'recetas.usuario_id', '=', 'users.id')
            ->join('categorias', 'recetas.categoria_id', '=', 'categorias.id')
            ->select('recetas.*', 'users.name as autor', 'users.avatar', 'categorias.nombre as categoria')
            ->orderBy('recetas.created_at', 'desc')
            ->get();

        $usuario_actual = DB::table('users')->where('id', 1)->first();

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
        // validaciones varias con mensajes personalizados
        $request->validate([
            'titulo' => 'required|max:150',
            'descripcion' => 'required',
            'url_imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tiempo_coccion' => 'required|integer|min:1',
            'pasos' => 'required|array',
            'pasos.0' => 'required'
        ], [
            'titulo.required' => 'El título de la receta es obligatorio.',
            'titulo.max' => 'El título es demasiado largo (máximo 150 letras).',
            'descripcion.required' => 'Debes escribir una breve descripción.',
            'url_imagen.required' => '¡No te olvides de subir una foto apetitosa!',
            'url_imagen.image' => 'El archivo debe ser una imagen válida.',
            'tiempo_coccion.required' => 'Indica el tiempo de preparación.',
            'pasos.0.required' => 'Debes escribir al menos el primer paso.'
        ]);
        // imagen por defecto por si el usuario no sube ninguna
        $ruta_imagen_bd = 'assets/img/logo.png';

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

    public function show($id)
    {
        $receta = DB::table('recetas')
            ->join('users', 'recetas.usuario_id', '=', 'users.id')
            ->join('categorias', 'recetas.categoria_id', '=', 'categorias.id')
            ->select('recetas.*', 'users.name as autor', 'users.avatar', 'categorias.nombre as categoria')
            ->where('recetas.id', $id)
            ->first();

        if (!$receta) {
            abort(404);
        }

        $comentarios = DB::table('comentarios')
            ->join('users', 'comentarios.usuario_id', '=', 'users.id')
            ->select('comentarios.*', 'users.name as nombre_usuario', 'users.avatar')
            ->where('comentarios.receta_id', $id)
            ->orderBy('comentarios.created_at', 'desc')
            ->get();

        $media = DB::table('valoraciones')
            ->where('receta_id', $id)
            ->avg('puntuacion') ?? 0;

        return view('detalle', [
            'receta' => $receta,
            'comentarios' => $comentarios,
            'media' => $media
        ]);
    }

    public function destroy($id)
    {
        $receta = DB::table('recetas')->where('id', $id)->first();

        // si no existe, error
        if (!$receta) {
            abort(404);
        }

        // Comprobar que el usuario tenga los permisos para borrar su receta
        if ($receta->usuario_id != 1) {
            abort(403, 'No tienes permiso para borrar la receta de otra persona.');
        }

        // Si la receta es suya, la borramos
        DB::table('recetas')->where('id', $id)->delete();

        return redirect('/');
    }

    // Formulario de edición con los datos antiguos
    public function edit($id)
    {
        $receta = DB::table('recetas')->where('id', $id)->first();

        if (!$receta) {
            abort(404);
        }

        // Ver si es el dueño
        if ($receta->usuario_id != 1) {
            abort(403, 'No tienes permiso para editar esta receta.');
        }
        return view('editar', ['receta' => $receta]);
    }

    public function update(Request $request, $id)
    {
        // La receta existe y es del usuario
        $receta = DB::table('recetas')->where('id', $id)->first();
        if (!$receta || $receta->usuario_id != 1) {
            abort(403, 'Acción no permitida.');
        }

        // Validar los datos del formulario pero no la foto ya que no es necesaria subir una nueva
        $request->validate([
            'titulo' => 'required|max:150',
            'descripcion' => 'required',
            'url_imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tiempo_coccion' => 'required|integer|min:1',
            'pasos' => 'required|array',
            'pasos.0' => 'required'
        ]);

        $pasos_texto_unificado = implode('. ', array_filter($request->pasos)) . '.';

        // Código por si el usuario decide subir una foto nueva
        $ruta_imagen_bd = $receta->url_imagen;
        if ($request->hasFile('url_imagen')) {
            $file = $request->file('url_imagen');
            $nombre_archivo = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img'), $nombre_archivo);
            $ruta_imagen_bd = 'assets/img/' . $nombre_archivo;
        }

        DB::table('recetas')->where('id', $id)->update([
            'categoria_id' => $request->categoria_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tiempo_preparacion' => $request->tiempo_coccion,
            'pasos' => $pasos_texto_unificado,
        ]);

        return redirect('/');
    }
}
