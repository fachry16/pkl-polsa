<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Krs extends Model
{
    protected $fillable = [
        'program_studi_id',
        'mata_kuliah_id',
        'dosen_id',
        'tahun_akademik_id',
        'kelas',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function mahasiswas()
    {
        return $this->belongsToMany(Mahasiswa::class, 'krs_mahasiswa')
            ->withTimestamps();
    }

    public function pengampu()
    {
        return $this->hasOne(Pengampu::class, 'krs_id');
    }
}
