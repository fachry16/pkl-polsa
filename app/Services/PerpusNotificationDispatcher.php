<?php

namespace App\Services;

use App\Jobs\SendPerpusNotificationJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        // Dengan QUEUE_CONNECTION=sync, dispatch mengeksekusi job secara inline
        // sehingga kegagalan webhook (perpus mati/timeout) akan melempar
        // exception ke request dosen dan menghentikan loop notifikasi
        // mahasiswa berikutnya. Kegagalan webhook tidak boleh menggagalkan
        // aksi utama LMS, jadi cukup dicatat sebagai warning. Pada queue async
        // dispatch hanya enqueue, sehingga retry ($tries/$backoff) tetap jalan.
        try {
            SendPerpusNotificationJob::dispatch($payload);
        } catch (Throwable $e) {
            Log::warning('Webhook notifikasi ke Perpus gagal dikirim, diabaikan.', [
                'nim' => $nim,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
