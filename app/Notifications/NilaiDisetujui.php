<?php

namespace App\Notifications;

use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NilaiDisetujui extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public string $penyetuju,
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
            'judul' => 'Nilai Disetujui: '.($this->pengampu->mataKuliah?->nama ?? ''),
            'penyetuju' => $this->penyetuju,
            'status' => 'Disetujui',
            'url' => route('lms.show', [$this->pengampu->id, 'tab' => 'rekap_nilai']),
        ];
    }
}
