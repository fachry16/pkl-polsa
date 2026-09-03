<?php

namespace App\Notifications;

use App\Models\LmsMateri;
use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MateriBaru extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public LmsMateri $materi,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'pengampu_id' => $this->pengampu->id,
            'mata_kuliah' => $this->pengampu->mataKuliah?->nama,
            'mata_kuliah_kode' => $this->pengampu->mataKuliah?->kode,
            'kelas' => $this->pengampu->kelas,
            'judul' => 'Materi Baru: '.$this->materi->judul,
            'url' => route('mahasiswa.lms.show', $this->pengampu->id).'?tab=tugas_kelas',
        ];
    }
}
