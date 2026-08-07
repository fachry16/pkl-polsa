<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    protected $fillable = [
        'tahun',
        'semester',
        'is_active',
    ];
    public function semesterMahasiswas() {
        return $this->hasMany(SemesterMahasiswa::class);
    }
    public function pengampus() {
        return $this->hasMany(Pengampu::class);
    }
    public function mahasiswaTahunAkademik() {
        return $this->hasMany(MahasiswaTahunAkademik::class);
    }
}
