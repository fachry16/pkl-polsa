<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = [
        'program_studi_id',
        'nim',
        'nama',
        'angkatan',
        'status',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function semesterMahasiswas()
    {
        return $this->hasMany(SemesterMahasiswa::class);
    }

    public function tahunAkademiks()
    {
        return $this->belongsToMany(TahunAkademik::class, 'semester_mahasiswas');
    }

    public function pengampus()
    {
        return $this->belongsToMany(Pengampu::class, 'pengampu_mahasiswa')
            ->withTimestamps();
    }
}
