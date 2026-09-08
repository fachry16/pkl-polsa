<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_DINILAI = 'dinilai';
    public const STATUS_FINAL = 'final';

    protected $fillable = [
        'pengampu_id',
        'status',
        'created_by',
    ];

    public function pengampu()
    {
        return $this->belongsTo(Pengampu::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DINILAI => 'Sudah Dinilai',
            self::STATUS_FINAL => 'Final',
            default => ucfirst($this->status),
        };
    }
}
