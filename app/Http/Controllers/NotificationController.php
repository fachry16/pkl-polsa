<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    public function markAsRead(DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id !== auth()->id(), 403);

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return Redirect::to($notification->data['url'] ?? route('dashboard'));
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('toast_success', 'Semua notifikasi telah dibaca.');
    }
}
