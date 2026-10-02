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

        $url = $notification->data['url'] ?? route('dashboard');

        // Pastikan redirect tetap berada pada host & port aktif saat ini (tidak terpental ke port 80/localhost)
        $parsed = parse_url($url);
        if (! empty($parsed['path'])) {
            $relativeUrl = $parsed['path']
                .(isset($parsed['query']) ? '?'.$parsed['query'] : '')
                .(isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '');

            return redirect()->to($relativeUrl);
        }

        return redirect()->to($url);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('toast_success', 'Semua notifikasi telah dibaca.');
    }
}
