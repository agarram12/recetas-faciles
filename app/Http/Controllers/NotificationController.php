<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Todas las notificaciones se han marcado como leídas.');
    }
}
