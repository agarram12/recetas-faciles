<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BienvenidaApp extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => '¡Te damos la bienvenida a Recetas Fáciles! Esperamos que disfrutes de la app.',
            'ruta' => route('inicio'),
            'tipo' => 'bienvenida',
        ];
    }
}
