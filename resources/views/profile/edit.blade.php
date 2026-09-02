@extends('layouts.app')

@section('content')

<div class="page-header" style="margin-bottom: 0.25rem;">
    Pengaturan Akun
</div>
<p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">
    Personalisasi foto profil akun Anda dan perbarui kata sandi secara berkala untuk menjaga keamanan akun.
</p>

<div class="mb-5" style="margin-bottom: 1.5rem;">
    <div class="card">
        @include('profile.partials.update-profile-information-form')
    </div>
</div>

<div class="mb-5" style="margin-bottom: 1.5rem;">
    <div class="card">
        @include('profile.partials.update-password-form')
    </div>
</div>

@endsection
