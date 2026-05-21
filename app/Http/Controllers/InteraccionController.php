<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Receta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Valoracion;
use App\Notifications\NuevoComentario;
use App\Notifications\NuevaValoracion;

class InteraccionController extends Controller
{

    public function comentar(Request $request, $id)
    {
        // validaciones del comentario
        $request->validate([
            'contenido' => 'required|string|max:500'
        ], [
            'contenido.required' => 'No puedes enviar un comentario vacío.',
            'contenido.max' => 'Tu comentario no puede superar los 500 caracteres.'
        ]);

        $comentario = Comentario::create([
            'receta_id' => $id,
            'usuario_id' => Auth::id(),
            'contenido' => $request->contenido
        ]);

        $receta = Receta::with('autor')->findOrFail($id);
        if ($receta->usuario_id !== Auth::id()) {
            $receta->autor->notify(new NuevoComentario(
                Auth::user(),
                $receta,
                $comentario->contenido
            ));
        }


        if ($request->ajax()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return response()->json([
                'success'   => true,
                'mensaje'   => '¡Gracias por compartir tu opinión!',
                'contenido' => e($comentario->contenido),
                'autor'     => e($user->name),
                'avatar'    => asset($user->avatar ?? 'assets/img/logo.png'),
                'fecha'     => $comentario->created_at->format('d/m/Y H:i'),
            ]);
        }

        return back()->with('success', '¡Gracias por compartir tu opinión!');
    }
    

    public function valorar(Request $request, $id)
    {
        // recibe 1 estrella o 5 como max
        $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5'
        ]);
        
        $valoracion = Valoracion::updateOrCreate(
            ['usuario_id' => Auth::id(), 'receta_id' => $id],
            ['puntuacion' => $request->puntuacion]
        );

        $receta = Receta::with('autor')->findOrFail($id);
        if ($receta->usuario_id !== Auth::id()) {
            $receta->autor->notify(new NuevaValoracion(
                Auth::user(),
                $receta,
                $valoracion->puntuacion
            ));
        }

        // Invalidar caché de populares (el ranking puede cambiar)
        Cache::forget('recetas_populares');


        if ($request->ajax()) {
            $nuevaMedia = Valoracion::where('receta_id', $id)->avg('puntuacion') ?? 0;
            return response()->json([
                'success'    => true,
                'mensaje'    => '¡Gracias por tu valoración!',
                'media'      => round($nuevaMedia, 1),
                'puntuacion' => $valoracion->puntuacion,
            ]);
        }

        return back()->with('success', '¡Gracias por tu valoración!');
    }

    // Añadir y quitar favoritos
    public function toggleFavorito(Request $request, $id)
    {
        /** @var \App\Models\User $usuario */
        $usuario = Auth::user();
        $resultado = $usuario->recetasFavoritas()->toggle($id);

        // Si es AJAX, devolver JSON sin recargar
        if ($request->ajax()) {
            $esFavorito = in_array($id, $resultado['attached']);
            return response()->json([
                'esFavorito' => $esFavorito,
                'mensaje'    => $esFavorito ? 'Receta guardada en favoritos' : 'Receta eliminada de favoritos',
            ]);
        }

        return back()->with('success', 'Tu lista de favoritos ha sido actualizada.');
    }
}