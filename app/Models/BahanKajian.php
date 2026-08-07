<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanKajian extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'kode_bk',
        'nama_bk',
        'referensi',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_bahan_kajian_mata_kuliah', 'bahan_kajian_id', 'cpl_id');
    }

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'bahan_kajian_mata_kuliah');
    }
}
