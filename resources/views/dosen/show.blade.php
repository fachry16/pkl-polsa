@extends('layouts.app')

@section('content')

<div class="profile-card">
    <div class="profile-head">
        <div class="profile-avatar">
            {{ strtoupper(substr($dosen->user->name, 0, 1)) }}
        </div>
        <div class="profile-head-info">
            <h1 class="profile-name">{{ $dosen->user->name }}</h1>
            <div class="profile-meta">
                <span class="profile-nidn">{{ $dosen->nidn }}</span>
                <span class="profile-badge {{ strtolower($dosen->jabatan) == 'kaprodi' ? 'badge-kaprodi' : 'badge-dosen' }}">
                    {{ strtolower($dosen->jabatan) == 'kaprodi' ? 'Kaprodi' : 'Dosen' }}
                </span>
            </div>
        </div>
    </div>
    <div class="profile-body">
        <div class="profile-field">
            <span class="profile-label">Email</span>
            <span class="profile-value">{{ $dosen->user->email }}</span>
        </div>
        <div class="profile-field">
            <span class="profile-label">Program Studi</span>
            <span class="profile-value">{{ $dosen->programStudi->nama_prodi }}</span>
        </div>
        <div class="profile-field">
            <span class="profile-label">NIDN</span>
            <span class="profile-value">{{ $dosen->nidn }}</span>
        </div>
    </div>
    <div class="profile-actions">
        <a href="{{ route('dosen.self.riwayat') }}" class="btn btn-primary">
            Lihat Riwayat Mengajar
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>

@endsection
