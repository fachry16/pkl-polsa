<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentApproval extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DIREVISI = 'direvisi';

    protected $fillable = [
        'assessment_id',
        'status',
        'diajukan_oleh',
        'diajukan_at',
        'disetujui_oleh',
        'disetujui_at',
        'catatan_revisi',
        'direvisi_oleh',
        'direvisi_at',
    ];

    protected $casts = [
        'diajukan_at' => 'datetime',
        'disetujui_at' => 'datetime',
        'direvisi_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function peninjau()
    {
        return $this->belongsTo(User::class, 'direvisi_oleh');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'Menunggu Persetujuan',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DIREVISI => 'Direvisi',
            default => $this->status,
        };
    }
}
