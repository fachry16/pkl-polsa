<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    protected $fillable = [
        'program_studi_id',
        'nama_kurikulum',
        'tahun_berlaku',
        'beban_studi',
        'deskripsi',
        'status',
    ];
    public function programStudi() {
        return $this->belongsTo(ProgramStudi::class);
    }
    public function profilLulusans() {
        return $this->hasMany(ProfilLulusan::class);
    }
    public function cpls() {
        return $this->hasMany(Cpl::class);
    }
    public function mataKuliahs() {
        return $this->hasMany(MataKuliah::class);
    }
    public function getTotalSksAttribute() {
        return $this->mataKuliahs->sum(function ($mk) {
            return $mk->sks_teori + $mk->sks_praktikum;
        });
    }
    public function bahanKajians() {
        return $this->hasMany(BahanKajian::class);
    }
    public function cpmks() {
        return $this->hasMany(Cpmk::class);
    }
}
