<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpsPenilaian extends Model
{
    protected $fillable = [
        'rps_id',
        'tugas',
        'quiz',
        'uts',
        'uas',
        'praktikum',
        'project',
    ];

    public function rps()
    {
        return $this->belongsTo(Rps::class);
    }
}
