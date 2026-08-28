<?php

namespace App\Notifications;

use App\Models\Rps;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RpsDiajukan extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Rps $rps,
        public string $pengaju,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'mata_kuliah' => $this->rps->mataKuliah?->nama,
            'mata_kuliah_kode' => $this->rps->mataKuliah?->kode,
            'judul' => 'RPS '.($this->rps->mataKuliah?->nama ?? ''),
            'pengaju' => $this->pengaju,
            'url' => route('rps.pengajuan'),
        ];
    }
}
