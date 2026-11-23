<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rps extends Model
{
    public const JUMLAH_PERTEMUAN = 14;

    protected $fillable = [
        'mata_kuliah_id',
        'kode_rps',
        'semester',
        'dosen_pengampu_id',
        'deskripsi_mata_kuliah',
        'sub_cpmk',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_revisi',
        'rumpun_mk_id',
        'mk_prasyarat',
        'prasyarat_untuk',
        'integrasi_antar_mk',
        'tautan_daring',
        'daftar_pustaka',
        'dosen_pengembang_rps_id',
        'koordinator_rmk_id',
        'ketua_prodi_id',
    ];

    protected $casts = [
        'tanggal_disetujui' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosenPengampu()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pengampu_id');
    }

    public function dosenPengembang()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pengembang_rps_id');
    }

    public function rumpunMk()
    {
        return $this->belongsTo(RumpunMk::class);
    }

    public function ketuaProdi()
    {
        return $this->belongsTo(Dosen::class, 'ketua_prodi_id');
    }

    public function koordinatorRmk()
    {
        return $this->belongsTo(Dosen::class, 'koordinator_rmk_id');
    }

    public function getNamaDosenPengampuAttribute(): ?string
    {
        return $this->dosenPengampu?->user?->name;
    }

    public function getNamaDosenPengembangAttribute(): ?string
    {
        return $this->dosenPengembang?->user?->name;
    }

    public function getNamaRumpunMkAttribute(): ?string
    {
        return $this->rumpunMk?->nama;
    }

    public function getNamaKetuaProdiAttribute(): ?string
    {
        return $this->ketuaProdi?->user?->name;
    }

    public function getNamaKoordinatorRmkAttribute(): ?string
    {
        return $this->koordinatorRmk?->user?->name;
    }

    public function pertemuans()
    {
        return $this->hasMany(RpsPertemuan::class);
    }

    public function penilaians()
    {
        return $this->hasMany(RpsPenilaian::class);
    }

    public function penilaian()
    {
        return $this->hasOne(RpsPenilaian::class);
    }

    public function bentukEvaluasis()
    {
        return $this->hasMany(RpsBentukEvaluasi::class);
    }

    public function tugas()
    {
        return $this->hasMany(RpsTugas::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function subCpmkList(): array
    {
        return collect(explode("\n", (string) $this->sub_cpmk))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    public function setSubCpmkList(array $items): void
    {
        $this->update([
            'sub_cpmk' => collect($items)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->implode("\n"),
        ]);
    }

    public function mingguKosong(): array
    {
        $terisi = $this->pertemuans()->pluck('minggu');

        return array_values(array_diff(range(1, self::JUMLAH_PERTEMUAN), $terisi->all()));
    }

    public function kelengkapanAjukan(): array
    {
        $mingguKosong = $this->mingguKosong();
        $tugasLengkap = $this->tugas()->count() > 0;
        $penilaianLengkap = (bool) $this->penilaian;

        return [
            'pertemuan' => $this->pertemuans()->count(),
            'tugas' => $this->tugas()->count(),
            'minggu_kosong' => $mingguKosong,
            'tugas_lengkap' => $tugasLengkap,
            'penilaian_lengkap' => $penilaianLengkap,
            'siap' => empty($mingguKosong) && $tugasLengkap && $penilaianLengkap,
        ];
    }
}
