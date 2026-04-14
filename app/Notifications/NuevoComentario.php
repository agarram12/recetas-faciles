<?php

namespace App\Notifications;

use App\Models\Receta;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NuevoComentario extends Notification
{
    use Queueable;

    public function __construct(public User $autor, public Receta $receta, public string $comentario)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => sprintf('%s comentó tu receta "%s".', $this->autor->name, $this->receta->titulo),
            'autor_id' => $this->autor->id,
            'autor_nombre' => $this->autor->name,
            'receta_id' => $this->receta->id,
            'ruta' => route('receta.show', $this->receta->id),
            'tipo' => 'comentario',
            'contenido' => $this->comentario,
        ];
    }
}
