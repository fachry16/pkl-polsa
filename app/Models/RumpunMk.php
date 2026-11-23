<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumpunMk extends Model
{
    protected $fillable = [
        'nama',
    ];

    public function rps()
    {
        return $this->hasMany(Rps::class);
    }
}
