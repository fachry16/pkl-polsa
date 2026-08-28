<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengampu extends Model
{
    protected $fillable = [
        'krs_id',
        'dosen_id',
        'mata_kuliah_id',
        'tahun_akademik_id',
        'semester_akademik',
        'kelas',
    ];

    public function krs()
    {
        return $this->belongsTo(Krs::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function mahasiswas()
    {
        return $this->belongsToMany(Mahasiswa::class, 'pengampu_mahasiswa')
            ->withTimestamps();
    }

    public function lmsMateris()
    {
        return $this->hasMany(LmsMateri::class);
    }

    public function lmsTugas()
    {
        return $this->hasMany(LmsTugas::class);
    }

    public function lmsSubmissions()
    {
        return $this->hasManyThrough(LmsSubmission::class, LmsTugas::class);
    }

    public function lmsForumDiskusis()
    {
        return $this->hasMany(LmsForumDiskusi::class);
    }

    public function lmsPengumumans()
    {
        return $this->hasMany(LmsPengumuman::class);
    }

    public function lmsSesiAbsensis()
    {
        return $this->hasMany(LmsSesiAbsensi::class);
    }

    public function rpsPertemuans()
    {
        return $this->mataKuliah?->rps?->pertemuans()->orderBy('minggu')->get() ?? collect();
    }
}
