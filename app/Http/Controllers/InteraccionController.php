<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InteraccionController extends Controller
{
    public function comentar(Request $request, $id)
    {
        $request->validate([
            'contenido' => 'required|min:3'
        ], [
            'contenido.required' => 'El comentario no puede estar vacío.',
            'contenido.min' => 'El comentario debe tener al menos 3 letras.'
        ]);

        DB::table('comentarios')->insert([
            'usuario_id' => 1,
            'receta_id' => $id,
            'contenido' => $request->contenido,
            'created_at' => now()
        ]);

        // Volvemos a la receta
        return redirect()->route('receta.show', $id);
    }
}