<?php

namespace App\Notifications;

use App\Models\Rps;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RpsDisetujui extends Notification
{
    use Queueable;

    public function __construct(
        public Rps $rps,
        public string $penyetuju,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'rps_id' => $this->rps->id,
            'mata_kuliah' => $this->rps->mataKuliah?->nama,
            'mata_kuliah_kode' => $this->rps->mataKuliah?->kode,
            'judul' => 'RPS Disetujui: '.($this->rps->mataKuliah?->nama ?? ''),
            'penyetuju' => $this->penyetuju,
            'status' => 'Disetujui',
            'url' => route('mata-kuliah.rps.index', $this->rps->mata_kuliah_id),
        ];
    }
}
