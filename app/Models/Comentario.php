<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';
    const UPDATED_AT = null;
    protected $fillable = ['receta_id', 'usuario_id', 'contenido'];
    public function autor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}