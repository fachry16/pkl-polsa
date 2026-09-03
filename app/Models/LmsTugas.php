<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsTugas extends Model
{
    protected $fillable = [
        'pengampu_id',
        'rps_pertemuan_id',
        'judul',
        'instruksi',
        'file_lampiran',
        'deadline',
        'bobot_nilai',
        'batas_upload_mb',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function rpsPertemuan()
    {
        return $this->belongsTo(RpsPertemuan::class);
    }

    public function submissions()
    {
        return $this->hasMany(LmsSubmission::class, 'lms_tugas_id');
    }

    public function komentars()
    {
        return $this->hasMany(LmsTopikKomentar::class, 'topik_id')->where('tipe_topik', 'tugas');
    }

    public function canBeModified(): bool
    {
        return $this->created_at ? $this->created_at->addHours(24)->isFuture() : false;
    }
}

