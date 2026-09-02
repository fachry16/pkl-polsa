<?php

namespace App\Notifications;

use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KhsDisetujuiNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Mahasiswa $mahasiswa,
        public TahunAkademik $tahunAkademik,
        public string $approverName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'KHS Semester Disetujui',
            'message' => "Kartu Hasil Studi (KHS) Anda untuk Tahun Akademik {$this->tahunAkademik->tahun} {$this->tahunAkademik->semester} telah disetujui oleh Kaprodi ({$this->approverName}).",
            'url' => route('khs.self'),
            'tahun_akademik_id' => $this->tahunAkademik->id,
            'type' => 'khs_approved',
        ];
    }
}
