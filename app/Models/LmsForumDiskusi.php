<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsForumDiskusi extends Model
{
    protected $fillable = [
        'pengampu_id',
        'user_id',
        'parent_id',
        'pesan',
        'file_path',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }
}
