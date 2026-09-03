<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodeBobotPenilaian extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'cpl_id',
        'mata_kuliah_id',
        'cpmk_id',
        'partisipasi',
        'kuis',
        'tugas_teori_individu',
        'unjuk_kerja_presentasi',
        'tes_tulis_uts',
        'tes_tulis_uas',
        'tugas_teori_kelompok',
        'tugas_praktikum',
        'responsi',
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
