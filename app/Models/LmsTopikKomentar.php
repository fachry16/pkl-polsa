<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsTopikKomentar extends Model
{
    protected $fillable = [
        'tipe_topik',
        'topik_id',
        'user_id',
        'mahasiswa_id',
        'is_private',
        'pesan',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function isWithinTimeLimit(int $minutes = 15): bool
    {
        return $this->created_at ? $this->created_at->addMinutes($minutes)->isFuture() : false;
    }
}
