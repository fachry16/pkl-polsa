<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'kode_cpmk',
        'deskripsi',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_cpmk_semesters', 'cpmk_id', 'cpl_id');
    }

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'cpmk_mata_kuliah');
    }
}
