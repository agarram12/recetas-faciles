<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'recetas';

    protected $fillable = [
        'usuario_id', 'categoria_id', 'titulo', 'descripcion', 
        'tiempo_coccion', 'dificultad', 'url_imagen', 'pasos'
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'receta_id');
    }

    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class, 'receta_id');
    }

    public function ingredientes()
    {
        return $this->belongsToMany(Ingrediente::class, 'receta_ingredientes', 'receta_id', 'ingrediente_id')
                    ->withPivot('cantidad');
    }
}