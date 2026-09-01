<?php

namespace App\Notifications;

use App\Models\LmsMateri;
use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MateriBaru extends Notification
{
    use Queueable;

    public $materi;
    public $pengampu;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pengampu $pengampu, LmsMateri $materi)
    {
        $this->pengampu = $pengampu;
        $this->materi = $materi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'judul' => 'Materi Baru Tersedia',
            'deskripsi' => 'Dosen telah mengunggah materi baru.',
            'mata_kuliah' => $this->pengampu->mataKuliah->nama ?? 'Mata Kuliah',
            'mata_kuliah_kode' => $this->pengampu->mataKuliah->kode ?? '',
            'kelas' => $this->pengampu->kelas ?? '-',
            'materi_judul' => $this->materi->judul ?? '-',
            'dosen' => $this->pengampu->dosen->user->name ?? 'Dosen',
        ];
    }
}
