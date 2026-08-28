<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterMahasiswa extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'semester',
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
}
