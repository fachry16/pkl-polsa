<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiKurikulum extends Model
{
    protected $fillable = [
        'kurikulum_id',
        'tahun_akademik_id',
        'judul',
        'catatan',
        'rekomendasi',
        'created_by',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
