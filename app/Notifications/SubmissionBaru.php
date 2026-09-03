<?php

namespace App\Notifications;

use App\Models\LmsTugas;
use App\Models\Mahasiswa;
use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SubmissionBaru extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public LmsTugas $tugas,
        public Mahasiswa $mahasiswa,
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
            'judul' => $this->tugas->judul,
            'mahasiswa' => $this->mahasiswa->nama,
            'url' => route('lms.tugas.show', [$this->pengampu->id, $this->tugas->id]),
        ];
    }
}
