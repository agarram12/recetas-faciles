<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Receta;
use Illuminate\Support\Facades\Auth;
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

        $receta = Receta::findOrFail($id);
        if ($receta->usuario_id !== Auth::id()) {
            $receta->autor->notify(new NuevoComentario(
                Auth::user(),
                $receta,
                $comentario->contenido
            ));
        }

        return back()->with('success', '¡Gracias por compartir tu opinión!');
    }
    
    // puntuaciones por estrellas
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

        $receta = Receta::findOrFail($id);
        if ($receta->usuario_id !== Auth::id()) {
            $receta->autor->notify(new NuevaValoracion(
                Auth::user(),
                $receta,
                $valoracion->puntuacion
            ));
        }

        return back()->with('success', '¡Gracias por tu valoración!');
    }

    // Añadir y quitar favoritos
    public function toggleFavorito($id)
    {
        /** @var \App\Models\User $usuario */
        $usuario = Auth::user();
        $usuario->recetasFavoritas()->toggle($id);

        return back()->with('success', 'Tu lista de favoritos ha sido actualizada.');
    }
}