<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpsBentukEvaluasi extends Model
{
    protected $fillable = [
        'rps_id',
        'bentuk_evaluasi',
        'sub_cpmk',
        'instrumen',
        'frekuensi',
        'tagihan',
        'bobot',
        'formatif',
        'sumatif',
    ];

    public function rps()
    {
        return $this->belongsTo(Rps::class);
    }
}
