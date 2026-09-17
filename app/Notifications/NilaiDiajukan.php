<?php

namespace App\Notifications;

use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NilaiDiajukan extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public string $pengaju,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'mata_kuliah' => $this->pengampu->mataKuliah?->nama,
            'mata_kuliah_kode' => $this->pengampu->mataKuliah?->kode,
            'kelas' => $this->pengampu->kelas,
            'judul' => 'Nilai Diajukan: '.($this->pengampu->mataKuliah?->nama ?? ''),
            'pengaju' => $this->pengaju,
            'url' => route('assessment.pengajuan'),
        ];
    }
}
