<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsInstrumenCpmk extends Model
{
    protected $table = 'lms_instrumen_cpmk';

    protected $fillable = [
        'pengampu_id',
        'cpmk_id',
        'komponen',
        'bobot_kontribusi',
    ];

    protected $casts = [
        'bobot_kontribusi' => 'decimal:2',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }
}
