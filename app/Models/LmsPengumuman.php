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

    public function canBeEdited(): bool
    {
        return $this->created_at ? $this->created_at->addMinutes(30)->isFuture() : false;
    }

    public function canBeDeleted(): bool
    {
        return $this->created_at ? $this->created_at->addHours(24)->isFuture() : false;
    }
}
