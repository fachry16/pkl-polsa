<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumusanNilaiAkhirMk extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'cpl_id',
        'mata_kuliah_id',
        'cpmk_id',
        'skor_maks',
        'total',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function cpl()
    {
        return $this->belongsTo(Cpl::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }
}
