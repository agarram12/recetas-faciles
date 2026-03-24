<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receta;
use Illuminate\Support\Facades\Auth;

class RecetaController extends Controller
{
    // Mostrar el feed
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $query = Receta::with(['autor', 'categoria']);

        if ($buscar) {
            $query->where('titulo', 'LIKE', '%' . $buscar . '%')
                  ->orWhere('descripcion', 'LIKE', '%' . $buscar . '%')
                  ->orWhereHas('categoria', function($q) use ($buscar) {
                      $q->where('nombre', 'LIKE', '%' . $buscar . '%');
                  });
        }

        $recetas = $query->orderBy('created_at', 'desc')->get();
        
        $populares = Receta::withAvg('valoraciones', 'puntuacion')
            ->orderBy('valoraciones_avg_puntuacion', 'desc')
            ->limit(3)
            ->get();

        return view('index', [
            'recetas' => $recetas,
            'populares' => $populares,
            'buscar' => $buscar
        ]);
    }

    // Formulario para crear una receta
    public function create()
    {
        $categorias = \App\Models\Categoria::all();
        return view('crear', ['categorias' => $categorias]);
    }

    // Guardar la nueva receta
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|max:150',
            'descripcion' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
            'url_imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tiempo_coccion' => 'required|integer|min:1',
            'dificultad' => 'required|in:Fácil,Media,Difícil',
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
        
        $ruta_imagen_bd = 'assets/img/logo.png';

        if ($request->hasFile('url_imagen')) {
            $file = $request->file('url_imagen');
            $nombre_archivo = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img'), $nombre_archivo);
            $ruta_imagen_bd = 'assets/img/' . $nombre_archivo;
        }

        $pasos_texto_unificado = implode('. ', array_filter($request->pasos)) . '.';

        // Insertar usando eloquent
        $receta = Receta::create([
            'usuario_id' => Auth::id() ?? 1,
            'categoria_id' => $request->categoria_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'pasos' => $pasos_texto_unificado,
            'url_imagen' => $ruta_imagen_bd,
            'tiempo_preparacion' => $request->tiempo_coccion,
            'dificultad' => $request->dificultad
        ]);

        return redirect('/')->with('success', 'Receta creada correctamente');

        return redirect('/')->with('success', 'Receta creada correctamente');
    }

    public function show($id)
    {
        // Cargar receta y comentarios
        $receta = Receta::with(['autor', 'categoria'])->findOrFail($id);
        $comentarios = \App\Models\Comentario::with('autor')
            ->where('receta_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular la media
        $media = \App\Models\Valoracion::where('receta_id', $id)->avg('puntuacion') ?? 0;

        return view('detalle', [
            'receta' => $receta,
            'comentarios' => $comentarios,
            'media' => $media
        ]);
    }

    public function destroy($id)
    {
        $receta = Receta::findOrFail($id);

        // Usar Auth::id para comprobar permisos
        if ($receta->usuario_id !== Auth::id() && Auth::id() !== 1) { 
            abort(403, 'No tienes permiso para borrar la receta de otra persona.');
        }

        $receta->delete();

        return redirect('/')->with('success', 'Receta eliminada.');
    }

    public function edit($id)
    {
        $receta = Receta::findOrFail($id);

        if ($receta->usuario_id !== Auth::id() && Auth::id() !== 1) {
            abort(403, 'No tienes permiso para editar esta receta.');
        }
        return view('editar', ['receta' => $receta]);
    }

    public function update(Request $request, $id)
    {
        $receta = Receta::findOrFail($id);

        if ($receta->usuario_id !== Auth::id() && Auth::id() !== 1) {
            abort(403, 'Acción no permitida.');
        }

        $request->validate([
            'titulo' => 'required|max:150',
            'descripcion' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
            'url_imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tiempo_coccion' => 'required|integer|min:1',
            'dificultad' => 'required|in:Fácil,Media,Difícil',
            'pasos' => 'required|array',
            'pasos.0' => 'required'
        ]);

        $pasos_texto_unificado = implode('. ', array_filter($request->pasos)) . '.';

        $ruta_imagen_bd = $receta->url_imagen;
        if ($request->hasFile('url_imagen')) {
            $file = $request->file('url_imagen');
            $nombre_archivo = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img'), $nombre_archivo);
            $ruta_imagen_bd = 'assets/img/' . $nombre_archivo;
        }

        // Actualizar con Eloquent
        $receta->update([
            'usuario_id' => Auth::id() ?? 1,
            'categoria_id' => $request->categoria_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'pasos' => $pasos_texto_unificado,
            'url_imagen' => $ruta_imagen_bd,
            'tiempo_preparacion' => $request->tiempo_coccion,
            'dificultad' => $request->dificultad
        ]);

        return redirect('/receta/' . $id)->with('success', 'Receta actualizada.');
    }
}