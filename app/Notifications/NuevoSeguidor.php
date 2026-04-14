<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevoSeguidor extends Notification
{
    use Queueable;

    public function __construct(public User $seguidor)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => sprintf('%s comenzó a seguirte.', $this->seguidor->name),
            'seguidor_id' => $this->seguidor->id,
            'seguidor_nombre' => $this->seguidor->name,
            'ruta' => route('usuario.show', $this->seguidor->id),
            'tipo' => 'seguimiento',
        ];
    }
}
