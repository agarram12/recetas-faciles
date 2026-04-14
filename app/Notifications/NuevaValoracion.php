<?php

namespace App\Notifications;

use App\Models\Receta;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevaValoracion extends Notification
{
    use Queueable;

    public function __construct(public User $autor, public Receta $receta, public int $puntuacion)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => sprintf('%s valoró tu receta "%s" con %s estrellas.', $this->autor->name, $this->receta->titulo, $this->puntuacion),
            'autor_id' => $this->autor->id,
            'autor_nombre' => $this->autor->name,
            'receta_id' => $this->receta->id,
            'ruta' => route('receta.show', $this->receta->id),
            'tipo' => 'valoracion',
            'puntuacion' => $this->puntuacion,
        ];
    }
}
