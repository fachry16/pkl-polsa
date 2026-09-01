@extends('layouts.app')

@section('content')

<div class="flex-header">
    <h1 class="page-header" style="margin: 0;">Cetak KRS Mahasiswa</h1>
    <a href="{{ route('krs.index') }}" class="btn btn-secondary">&larr; Kembali</a>
</div>

<x-alert type="error" :message="session('error')" />

<div style="max-width: 560px;">
    <div class="card">
        <h3 style="margin: 0 0 1rem; font-size: 1rem; font-weight: 600;">Pilih Mahasiswa</h3>

        <p style="font-size: 0.8rem; color: #6b7280; margin: 0 0 1rem;">
            Pilih program studi, tahun akademik, lalu mahasiswa untuk mencetak KRS yang mengumpulkan seluruh mata kuliah per kelas.
        </p>

        <form action="{{ route('krs.pilih-mahasiswa') }}" method="POST" id="formCetak">
            @csrf

            <div class="form-group">
                <label class="form-label">Program Studi</label>
                <select name="program_studi_id" id="program_studi_id" class="form-select" required>
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($programStudis as $prodi)
                        <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                @error('program_studi_id')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tahun Akademik</label>
                <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select" required>
                    <option value="">-- Pilih Tahun Akademik --</option>
                    @foreach($tahunAkademiks as $tahun)
                        <option value="{{ $tahun->id }}">{{ $tahun->tahun }} {{ $tahun->semester }}</option>
                    @endforeach
                </select>
                @error('tahun_akademik_id')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Mahasiswa</label>
                <select name="mahasiswa_id" id="mahasiswa_id" class="form-select" required disabled>
                    <option value="">-- Pilih Program Studi terlebih dahulu --</option>
                </select>
                @error('mahasiswa_id')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary" id="btnCetak" disabled>
                    Lanjutkan &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const prodiSelect = document.getElementById('program_studi_id');
    const mhsSelect = document.getElementById('mahasiswa_id');
    const btnCetak = document.getElementById('btnCetak');

    prodiSelect.addEventListener('change', function () {
        mhsSelect.innerHTML = '<option value="">Memuat mahasiswa...</option>';
        mhsSelect.disabled = true;
        btnCetak.disabled = true;

        if (!this.value) {
            mhsSelect.innerHTML = '<option value="">-- Pilih Program Studi terlebih dahulu --</option>';
            return;
        }

        fetch('{{ route("krs.mahasiswa-options") }}?program_studi_id=' + encodeURIComponent(this.value))
            .then(r => r.json())
            .then(data => {
                mhsSelect.innerHTML = '<option value="">-- Pilih Mahasiswa --</option>';
                data.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.label;
                    mhsSelect.appendChild(opt);
                });
                mhsSelect.disabled = false;
            })
            .catch(() => {
                mhsSelect.innerHTML = '<option value="">Gagal memuat mahasiswa.</option>';
            });
    });

    mhsSelect.addEventListener('change', function () {
        btnCetak.disabled = !this.value;
    });
</script>
@endpush

<style>
.flex-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}
</style>

@endsection
