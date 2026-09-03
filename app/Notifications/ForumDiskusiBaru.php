<?php

namespace App\Notifications;

use App\Models\LmsForumDiskusi;
use App\Models\Pengampu;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ForumDiskusiBaru extends Notification
{
    use Queueable;

    public function __construct(
        public Pengampu $pengampu,
        public LmsForumDiskusi $forum,
        public string $penulis,
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
            'judul' => 'Diskusi Baru: '.$this->forum->judul,
            'penulis' => $this->penulis,
            'url' => route('lms.show', $this->pengampu->id).'?tab=forum',
        ];
    }
}
