<?php

namespace App\Notifications;

use App\Models\Krs;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KrsBaruAdmin extends Notification
{
    use Queueable;

    public function __construct(
        public Krs $krs,
        public string $pembuat,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'krs_id' => $this->krs->id,
            'mata_kuliah' => $this->krs->mataKuliah?->nama,
            'mata_kuliah_kode' => $this->krs->mataKuliah?->kode,
            'kelas' => $this->krs->kelas,
            'judul' => 'KRS Baru: '.($this->krs->mataKuliah?->nama ?? 'Mata Kuliah'),
            'pembuat' => $this->pembuat,
            'url' => route('krs.show', $this->krs->id),
        ];
    }
}
