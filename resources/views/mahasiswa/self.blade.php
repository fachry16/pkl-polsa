@extends('layouts.app')

@section('content')

<div class="profile-card">
    <div class="profile-head">
        @if($mahasiswa->user->avatar_url)
            <img src="{{ $mahasiswa->user->avatar_url }}" alt="{{ $mahasiswa->nama }}" class="profile-avatar" style="object-fit: cover;">
        @else
            <div class="profile-avatar">
                {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
            </div>
        @endif
        <div class="profile-head-info">
            <h1 class="profile-name">{{ $mahasiswa->nama }}</h1>
            <div class="profile-meta">
                <span class="profile-nidn">{{ $mahasiswa->nim }}</span>
                <span class="profile-badge badge-dosen" style="background: #e0e7ff; color: #3730a3;">
                    Mahasiswa {{ $mahasiswa->jenis_kelas }}
                </span>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="profile-field">
            <span class="profile-label">NIM</span>
            <span class="profile-value">{{ $mahasiswa->nim }}</span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Email</span>
            <span class="profile-value">{{ $mahasiswa->user->email ?? '—' }}</span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Program Studi</span>
            <span class="profile-value">{{ $mahasiswa->programStudi->nama_prodi ?? '—' }}</span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Angkatan</span>
            <span class="profile-value">{{ $mahasiswa->angkatan }}</span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Jenis Kelas</span>
            <span class="profile-value">{{ $mahasiswa->jenis_kelas }}</span>
        </div>
        @if($semesterAktif)
        <div class="profile-field">
            <span class="profile-label">Semester Aktif</span>
            <span class="profile-value">Semester {{ $semesterAktif->semester }} ({{ $semesterAktif->tahunAkademik->nama_tahun ?? '—' }})</span>
        </div>
        @endif
    </div>

    <div class="profile-actions">
        <a href="{{ route('mahasiswa.lms.index') }}" class="btn btn-primary">
            Kelas LMS Saya
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-secondary">
            Pengaturan Akun &amp; Password
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>

@endsection
