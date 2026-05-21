<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receta;
use App\Http\Requests\RecetaStoreRequest;
use App\Http\Requests\RecetaUpdateRequest;
use App\Services\ImagenRecetaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RecetaController extends Controller
{
    /**
     * Servicio de gestión de imágenes inyectado.
     */
    protected ImagenRecetaService $imagenService;

    public function __construct(ImagenRecetaService $imagenService)
    {
        $this->imagenService = $imagenService;
    }

    // Mostrar el feed y búsqueda
    public function index(Request $request)
    {
        $buscar     = $request->input('buscar');
        $categoria  = $request->input('categoria');
        $dificultad = $request->input('dificultad');
        $tiempo     = $request->input('tiempo');
        $orden      = $request->input('orden', 'recientes');

        // Búsqueda de creadores coincidentes
        $usuariosEncontrados = [];
        if ($buscar) {
            $usuariosEncontrados = \App\Models\User::where('name', 'LIKE', '%' . $buscar . '%')
                ->withCount('recetas')
                ->limit(3)
                ->get();
        }

        $query = Receta::with(['autor', 'categoria']);

        // Filtro por búsqueda de texto
        if ($buscar) {
            $query->where(function ($sub) use ($buscar) {
                $sub->where('recetas.titulo', 'LIKE', '%' . $buscar . '%')
                    ->orWhere('recetas.descripcion', 'LIKE', '%' . $buscar . '%')
                    ->orWhereHas('autor', function ($q) use ($buscar) {
                        $q->where('name', 'LIKE', '%' . $buscar . '%');
                    })
                    ->orWhereHas('categoria', function ($q) use ($buscar) {
                        $q->where('nombre', 'LIKE', '%' . $buscar . '%');
                    });
            });
        }

        // Filtro por categoría específica
        if ($categoria) {
            $query->where('recetas.categoria_id', $categoria);
        }

        // Filtro por dificultad
        if ($dificultad) {
            $query->where('recetas.dificultad', $dificultad);
        }

        // Filtro por rango de tiempo de cocción
        if ($tiempo) {
            switch ($tiempo) {
                case 'rapido':    $query->where('recetas.tiempo_coccion', '<=', 15); break;
                case 'medio':    $query->whereBetween('recetas.tiempo_coccion', [16, 45]); break;
                case 'largo':    $query->whereBetween('recetas.tiempo_coccion', [46, 90]); break;
                case 'elaborado': $query->where('recetas.tiempo_coccion', '>', 90); break;
            }
        }

        // Si no hay filtros ni búsqueda, aplicar feed de seguidos
        if (!$buscar && !$categoria && !$dificultad && !$tiempo && Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $seguidos = $user->seguidos()->pluck('seguido_id')->toArray();

            if (! empty($seguidos)) {
                $query->where(function ($sub) use ($seguidos) {
                    $sub->whereIn('recetas.usuario_id', $seguidos)
                        ->orWhere('recetas.usuario_id', Auth::id());
                });
            }
        }

        // Ordenación
        switch ($orden) {
            case 'antiguos':  $query->orderBy('recetas.id', 'asc'); break;
            case 'rapidos':   $query->orderBy('recetas.tiempo_coccion', 'asc'); break;
            case 'lentos':    $query->orderBy('recetas.tiempo_coccion', 'desc'); break;
            default:          $query->orderByDesc('recetas.id'); break;
        }

        $recetas = $query->paginate(8);

        // Precargar IDs de favoritos del usuario 
        $favoritoIds = [];
        if (Auth::check()) {
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();
            $favoritoIds = $authUser->recetasFavoritas()->pluck('receta_id')->toArray();
        }

        // Si es petición AJAX, devolver solo las cards HTML
        if ($request->ajax()) {
            $html = '';

            // Si hay búsqueda y creadores coincidentes en la primera página
            if ($buscar && count($usuariosEncontrados) > 0 && $request->input('page', 1) == 1) {
                $html .= view('partials.creadores-busqueda', ['usuarios' => $usuariosEncontrados])->render();
            }

            foreach ($recetas as $receta) {
                $html .= view('partials.receta-card', [
                    'receta'      => $receta,
                    'favoritoIds' => $favoritoIds,
                ])->render();
            }

            return response()->json([
                'html'     => $html,
                'hasMore'  => $recetas->hasMorePages(),
                'nextPage' => $recetas->currentPage() + 1,
                'total'    => $recetas->total(),
            ]);
        }

        // Datos para los filtros del sidebar (cacheados)
        $categorias = Cache::remember('categorias_all', 3600, function () {
            return \App\Models\Categoria::all();
        });

        $populares = Cache::remember('recetas_populares', 3600, function () {
            return Receta::with('autor')
                ->withAvg('valoraciones', 'puntuacion')
                ->orderBy('valoraciones_avg_puntuacion', 'desc')
                ->limit(3)
                ->get();
        });

        return view('index', [
            'recetas'     => $recetas,
            'populares'   => $populares,
            'categorias'  => $categorias,
            'favoritoIds' => $favoritoIds,
            'buscar'      => $buscar,
            'categoria'   => $categoria,
            'dificultad'  => $dificultad,
            'tiempo'      => $tiempo,
            'orden'       => $orden,
            'usuariosEncontrados' => $usuariosEncontrados,
        ]);
    }

    // Formulario para crear una receta
    public function create()
    {
        $categorias = Cache::remember('categorias_all', 3600, function () {
            return \App\Models\Categoria::all();
        });
        return view('crear', ['categorias' => $categorias]);
    }

    /**
     * Guardar la nueva receta.
     */
    public function store(RecetaStoreRequest $request)
    {
        // Subir imagen principal a través del servicio
        $rutaImagen = $this->imagenService->subirImagenPrincipal(
            $request->file('url_imagen')
        );

        // Procesar pasos como texto unificado
        $pasos_texto_unificado = implode('. ', array_filter($request->pasos)) . '.';

        // Subir imágenes de pasos (si hay)
        $imagenesPasos = [];
        if ($request->hasFile('imagenes_pasos')) {
            $imagenesPasos = $this->imagenService->subirImagenesPasos(
                $request->file('imagenes_pasos')
            );
        }

        // Insertar usando Eloquent
        Receta::create([
            'usuario_id'     => Auth::id() ?? 1,
            'categoria_id'   => $request->categoria_id,
            'titulo'         => $request->titulo,
            'descripcion'    => $request->descripcion,
            'pasos'          => $pasos_texto_unificado,
            'url_imagen'     => $rutaImagen,
            'tiempo_coccion' => $request->tiempo_coccion,
            'dificultad'     => $request->dificultad,
            'imagenes_pasos' => !empty($imagenesPasos) ? $imagenesPasos : null,
        ]);

        // Invalidar cachés afectadas
        Cache::forget('recetas_populares');

        return redirect('/')->with('success', 'Receta creada correctamente');
    }

    public function show($id)
    {
        // Cargar receta con relaciones y agregados en una sola query
        $receta = Receta::with(['autor', 'categoria', 'comentarios.autor'])
            ->withAvg('valoraciones', 'puntuacion')
            ->withCount('comentarios')
            ->findOrFail($id);

        // La media viene del withAvg
        $media = $receta->valoraciones_avg_puntuacion ?? 0;

        return view('detalle', [
            'receta'      => $receta,
            'comentarios' => $receta->comentarios->sortByDesc('created_at'),
            'media'       => $media,
        ]);
    }

    /**
     * Eliminar receta.
     */
    public function destroy($id)
    {
        $receta = Receta::findOrFail($id);

        // Usar Auth::id para comprobar permisos
        if ($receta->usuario_id !== Auth::id() && Auth::id() !== 1) { 
            abort(403, 'No tienes permiso para borrar la receta de otra persona.');
        }

        // Eliminar todas las imágenes del sistema de ficheros
        $this->imagenService->eliminarTodasImagenes($receta);

        $receta->delete();

        // Invalidar cachés afectadas
        Cache::forget('recetas_populares');

        return redirect('/')->with('success', 'Receta eliminada.');
    }

    public function edit($id)
    {
        $receta = Receta::findOrFail($id);

        if ($receta->usuario_id !== Auth::id() && Auth::id() !== 1) {
            abort(403, 'No tienes permiso para editar esta receta.');
        }

        $categorias = Cache::remember('categorias_all', 3600, function () {
            return \App\Models\Categoria::all();
        });

        return view('editar', [
            'receta' => $receta, 
            'categorias' => $categorias
        ]);
    }

    /**
     * Actualizar receta.
     */
    public function update(RecetaUpdateRequest $request, $id)
    {
        $receta = Receta::findOrFail($id);

        if ($receta->usuario_id !== Auth::id() && Auth::id() !== 1) {
            abort(403, 'Acción no permitida.');
        }

        $pasos_texto_unificado = implode('. ', array_filter($request->pasos)) . '.';

        // Gestión de imagen principal
        $ruta_imagen_bd = $receta->url_imagen;
        if ($request->hasFile('url_imagen')) {
            // Eliminar la imagen anterior
            $this->imagenService->eliminarImagen($receta->url_imagen);
            // Subir la nueva
            $ruta_imagen_bd = $this->imagenService->subirImagenPrincipal(
                $request->file('url_imagen')
            );
        }

        // Gestión de imágenes de pasos
        $imagenesPasos = $receta->imagenes_pasos ?? [];
        if ($request->hasFile('imagenes_pasos')) {
            $imagenesPasos = $this->imagenService->subirImagenesPasos(
                $request->file('imagenes_pasos'),
                $imagenesPasos
            );
        }

        // Actualizar con Eloquent
        $receta->update([
            'usuario_id'     => Auth::id() ?? 1,
            'categoria_id'   => $request->categoria_id,
            'titulo'         => $request->titulo,
            'descripcion'    => $request->descripcion,
            'pasos'          => $pasos_texto_unificado,
            'url_imagen'     => $ruta_imagen_bd,
            'tiempo_coccion' => $request->tiempo_coccion,
            'dificultad'     => $request->dificultad,
            'imagenes_pasos' => !empty($imagenesPasos) ? $imagenesPasos : null,
        ]);

        // Invalidar cachés afectadas
        Cache::forget('recetas_populares');

        return redirect('/receta/' . $id)->with('success', 'Receta actualizada.');
    }
}