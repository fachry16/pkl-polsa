<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpsPertemuan extends Model
{
    protected $fillable = [
        'rps_id',
        'minggu',
        'sub_cpmk',
        'materi',
        'metode',
        'pengalaman_belajar',
        'indikator',
        'bobot',
    ];

    public function rps()
    {
        return $this->belongsTo(Rps::class);
    }

    public function lmsMateris()
    {
        return $this->hasMany(LmsMateri::class);
    }

    public function lmsTugas()
    {
        return $this->hasMany(LmsTugas::class);
    }
}
