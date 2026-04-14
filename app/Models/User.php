<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'descripcion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'usuario_id');
    }

    public function recetasFavoritas()
    {
        return $this->belongsToMany(\App\Models\Receta::class, 'favoritos', 'usuario_id', 'receta_id');
    }

    public function seguidores()
    {
        return $this->belongsToMany(self::class, 'seguidores', 'seguido_id', 'seguidor_id')->withTimestamps();
    }

    public function seguidos()
    {
        return $this->belongsToMany(self::class, 'seguidores', 'seguidor_id', 'seguido_id')->withTimestamps();
    }

    public function sigueA(User $usuario)
    {
        return $this->seguidos()->where('seguido_id', $usuario->id)->exists();
    }

    public function feedRecetas()
    {
        $seguidos = $this->seguidos()->pluck('seguido_id')->toArray();

        return \App\Models\Receta::where(function ($query) use ($seguidos) {
            if (! empty($seguidos)) {
                $query->whereIn('usuario_id', $seguidos);
            }
            $query->orWhere('usuario_id', $this->id);
        });
    }
}