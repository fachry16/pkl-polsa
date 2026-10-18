<?php

namespace App\Services;

use App\Jobs\SendPerpusNotificationJob;
use App\Models\User;

class PerpusNotificationDispatcher
{
    /**
     * Kirim notifikasi LMS ke backend perpus via background queue (untuk diteruskan via FCM ke mobile).
     *
     * @param  User  $user  User penerima di Eduva
     * @param  string  $type  Tipe event (tugas_baru, materi_baru, forum_baru, pengumuman_baru, nilai_diberikan)
     * @param  string  $title  Judul notifikasi
     * @param  string  $body  Isi ringkas notifikasi
     * @param  array<string, mixed>  $metadata  Data tambahan terkait LMS
     */
    public static function dispatch(User $user, string $type, string $title, string $body, array $metadata = []): void
    {
        $webhookUrl = config('services.perpus.webhook_url');

        if (empty($webhookUrl)) {
            return;
        }

        // Ambil NIM mahasiswa jika user adalah mahasiswa
        $nim = $user->mahasiswa?->nim;
        if (! $nim) {
            return;
        }

        $payload = [
            'nim' => $nim,
            'user_name' => $user->name,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'metadata' => $metadata,
            'timestamp' => now()->toIso8601String(),
        ];

        SendPerpusNotificationJob::dispatch($payload);
    }
}
