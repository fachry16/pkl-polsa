<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    protected $fillable = [
        'assessment_id',
        'mahasiswa_id',
        'cpmk_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }
}
