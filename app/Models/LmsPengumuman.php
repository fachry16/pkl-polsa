<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsPengumuman extends Model
{
    protected $table = 'lms_pengumumans';

    protected $fillable = [
        'pengampu_id',
        'judul',
        'isi',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }
}
