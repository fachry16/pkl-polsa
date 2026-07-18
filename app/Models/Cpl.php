<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'kode_cpl',
        'deskripsi',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function profilLulusans()
    {
        return $this->belongsToMany(ProfilLulusan::class, 'profil_lulusan_cpl');
    }

    public function bahanKajians()
    {
        return $this->belongsToMany(BahanKajian::class, 'cpl_bahan_kajian_mata_kuliah', 'cpl_id', 'bahan_kajian_id');
    }

    public function cpmks()
    {
        return $this->belongsToMany(Cpmk::class, 'cpl_cpmk_semesters', 'cpl_id', 'cpmk_id');
    }

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'cpl_bahan_kajian_mata_kuliah', 'cpl_id', 'mata_kuliah_id');
    }
}
