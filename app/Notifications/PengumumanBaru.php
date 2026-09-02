<?php

namespace App\Notifications;

use App\Models\LmsPengumuman;
use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PengumumanBaru extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public LmsPengumuman $pengumuman,
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
            'judul' => 'Pengumuman: '.$this->pengumuman->judul,
            'url' => route('mahasiswa.lms.show', $this->pengampu->id).'?tab=forum',
        ];
    }
}
