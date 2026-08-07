<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'kode',
        'nama',
        'sks_teori',
        'sks_praktikum',
        'semester',
        'jenis',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function getTotalSksAttribute()
    {
        return $this->sks_teori + $this->sks_praktikum;
    }

    public function bahanKajians()
    {
        return $this->belongsToMany(BahanKajian::class, 'bahan_kajian_mata_kuliah');
    }

    public function cpmks()
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_mata_kuliah');
    }

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_bahan_kajian_mata_kuliah', 'mata_kuliah_id', 'cpl_id');
    }

    public function pengampus()
    {
        return $this->hasMany(Pengampu::class);
    }

    public function rps()
    {
        return $this->hasOne(Rps::class);
    }
}
