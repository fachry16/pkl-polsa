<?php

namespace App\Notifications;

use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NilaiDirevisi extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public string $catatanRevisi,
        public string $peninjau,
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
            'judul' => 'Nilai Butuh Revisi: '.($this->pengampu->mataKuliah?->nama ?? ''),
            'peninjau' => $this->peninjau,
            'catatan' => $this->catatanRevisi,
            'status' => 'Direvisi',
            'url' => route('lms.show', [$this->pengampu->id, 'tab' => 'rekap_nilai']),
        ];
    }
}
