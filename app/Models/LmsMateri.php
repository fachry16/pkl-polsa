<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsMateri extends Model
{
    protected $fillable = [
        'pengampu_id',
        'rps_pertemuan_id',
        'judul',
        'deskripsi',
        'file_path',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function rpsPertemuan()
    {
        return $this->belongsTo(RpsPertemuan::class);
    }

    public function progress()
    {
        return $this->hasMany(LmsMateriMahasiswa::class, 'materi_id');
    }

    public function dibacaOleh(Mahasiswa $mahasiswa): bool
    {
        return $this->progress()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereNotNull('dibaca_pada')
            ->exists();
    }

    public function komentars()
    {
        return $this->hasMany(LmsTopikKomentar::class, 'topik_id')->where('tipe_topik', 'materi');
    }

    public function canBeModified(): bool
    {
        return $this->created_at ? $this->created_at->addHours(24)->isFuture() : false;
    }
}

