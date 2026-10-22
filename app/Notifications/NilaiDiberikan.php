<?php

namespace App\Notifications;

use App\Models\LmsSubmission;
use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NilaiDiberikan extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public LmsSubmission $submission,
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
            'judul' => $this->submission->lmsTugas->judul,
            'nilai' => $this->submission->nilai,
            'url' => route('mahasiswa.lms.show', $this->pengampu->id),
        ];
    }
}
