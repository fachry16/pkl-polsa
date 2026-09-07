@extends('layouts.app')

@section('content')

<div class="page-header">
    Tugas - {{ $pengampu->mataKuliah->kode ?? '' }} {{ $pengampu->mataKuliah->nama ?? '' }}
</div>

<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'tugas_kelas']) }}" class="btn btn-secondary btn-sm">Kembali ke Tugas Kelas</a>
    <a href="{{ route('lms.show', [$pengampu->id, 'tab' => 'rekap_nilai']) }}" class="btn btn-secondary btn-sm">Rekap Nilai</a>
</div>

@if($pengampu->lmsTugas->where('is_active', false)->count() > 0)
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.25rem; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.6rem;">
        <span style="font-size: 1.1rem;">🔴</span>
        <div>
            <strong>Draf Tugas RPS Perlu Konfirmasi:</strong> Terdapat {{ $pengampu->lmsTugas->where('is_active', false)->count() }} draf tugas hasil pengiriman dari RPS. Klik tombol <strong>"🚀 Tugaskan"</strong> pada draf di bawah untuk mengaktifkannya ke mahasiswa.
        </div>
    </div>
@endif

<div style="display: grid; grid-template-columns: 360px 1fr; gap: 1.5rem; align-items: start;">

    {{-- Card Pengarah RPS Tugas --}}
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
            <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #1e293b;">Manajemen Tugas LMS</h3>
            <span style="font-size: 0.7rem; background: #eef2ff; color: #4f46e5; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600;">Berbasis RPS</span>
        </div>

        <p style="font-size: 0.82rem; color: #475569; line-height: 1.6; margin-bottom: 1.25rem;">
            Pembuatan, pengeditan, dan pengunggahan file soal rancangan tugas dilakukan secara terpusat melalui dokumen <strong>RPS Mata Kuliah</strong>.
        </p>

        @if($pengampu->mataKuliah?->rps)
            <a href="{{ route('rps.tugas.index', $pengampu->mataKuliah->rps->id) }}" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Kelola Rancangan Tugas di RPS
            </a>
        @endif

        <a href="{{ route('mata-kuliah.rps.index', $pengampu->mata_kuliah_id) }}" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            Buka RPS Mata Kuliah
        </a>

        <div style="font-size: 0.75rem; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 8px; margin-top: 1.25rem; line-height: 1.5;">
            💡 <strong>Panduan:</strong> Unggah rancangan tugas dari RPS. Setelah dikonfirmasi, draf tugas akan muncul di daftar sebelah kanan. Klik tombol <strong>"🚀 Tugaskan"</strong> untuk mempublikasikannya ke mahasiswa.
        </div>
    </div>

    {{-- Daftar Tugas LMS yang Sudah Dibuat --}}
    <div>
        @forelse($tugas as $item)
            <div style="background: {{ ! $item->is_active ? '#fff5f5' : '#fff' }}; border-radius: 12px; border: 1px solid {{ ! $item->is_active ? '#fecaca' : '#e2e8f0' }}; padding: 1.25rem; transition: all 0.2s; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);"
                 onmouseover="this.style.borderColor='#c7d2fe';this.style.boxShadow='0 4px 16px rgba(79,70,229,0.08)';"
                 onmouseout="this.style.borderColor='{{ ! $item->is_active ? '#fecaca' : '#e2e8f0' }}';this.style.boxShadow='none';">
                <a href="{{ route('lms.tugas.show', [$pengampu->id, $item->id]) }}" style="display: block; text-decoration: none;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: {{ ! $item->is_active ? '#fee2e2' : '#f1f5f9' }}; color: {{ ! $item->is_active ? '#dc2626' : '#475569' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="font-weight: 600; font-size: 0.9rem; color: #0f172a;">{{ $item->judul }}</span>
                                @if($item->rps_pertemuan_id)
                                    <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Minggu {{ $item->rpsPertemuan->minggu ?? '?' }}</span>
                                @endif
                                @if(! $item->is_active)
                                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.12rem 0.55rem; font-size: 0.68rem; font-weight: 700;">🔴 Draf (Perlu Konfirmasi)</span>
                                @elseif($item->deadline->isPast())
                                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Tutup</span>
                                @else
                                    <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.65rem; font-weight: 600;">Aktif</span>
                                @endif
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                                Deadline: {{ $item->deadline->format('d M Y H:i') }} &middot; Bobot: {{ $item->bobot_nilai }} &middot; {{ $item->submissions_count }} pengumpulan
                            </div>
                        </div>
                    </div>
                </a>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid {{ ! $item->is_active ? '#fecaca' : '#f1f5f9' }};">
                    @if(! $item->is_active)
                        <form action="{{ route('lms.tugas.tugaskan', [$pengampu->id, $item->id]) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" style="background: #dc2626; border-color: #dc2626;" onclick="return confirm('Aktifkan dan tugaskan rancangan ini ke mahasiswa di kelas ini?')">
                                🚀 Tugaskan
                            </button>
                        </form>
                    @endif
                    @if($item->canBeModified())
                        @if($item->rps_tugas_id && $pengampu->mataKuliah?->rps)
                            <a href="{{ route('rps.tugas.edit', [$pengampu->mataKuliah->rps->id, $item->rps_tugas_id]) }}" class="btn btn-secondary btn-sm">Perbarui</a>
                        @else
                            <a href="{{ route('mata-kuliah.rps.index', $pengampu->mata_kuliah_id) }}" class="btn btn-secondary btn-sm">Perbarui</a>
                        @endif
                        <form action="{{ route('lms.tugas.destroy', ['pengampu' => $pengampu->id, 'tugas' => $item->id]) }}" method="POST" style="margin: 0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tugas ini beserta seluruh pengumpulannya?')">Hapus</button>
                        </form>
                    @else
                        <span style="font-size: 0.72rem; color: #94a3b8;">Terkunci (lewat 1x24 jam)</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; padding: 3rem;">
                <p style="color: #94a3b8; font-size: 0.9rem;">Belum ada tugas yang dibuat di kelas ini.</p>
            </div>
        @endforelse

        <div style="margin-top: 1rem;">
            {{ $tugas->links() }}
        </div>
    </div>
</div>

@endsection
