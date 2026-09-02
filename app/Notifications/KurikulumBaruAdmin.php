<?php

namespace App\Notifications;

use App\Models\Kurikulum;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KurikulumBaruAdmin extends Notification
{
    use Queueable;

    public function __construct(
        public Kurikulum $kurikulum,
        public string $pembuat,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'kurikulum_id' => $this->kurikulum->id,
            'nama_kurikulum' => $this->kurikulum->nama_kurikulum,
            'program_studi' => $this->kurikulum->programStudi?->nama_prodi,
            'tahun_berlaku' => $this->kurikulum->tahun_berlaku,
            'judul' => 'Kurikulum Baru: '.$this->kurikulum->nama_kurikulum,
            'pembuat' => $this->pembuat,
            'url' => route('program-studi.kurikulum', $this->kurikulum->program_studi_id),
        ];
    }
}
