@extends('layouts.app')

@section('content')

    <h1 class="page-header">
        Role &amp; Jabatan
    </h1>

<div class="mb-4">
    <a href="{{ route('roles.create') }}"
       class="btn btn-primary">
        Tambah Role &amp; Jabatan
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>Nama Role / Jabatan</th>
                <th>Kode (Slug)</th>
                <th>Deskripsi</th>
                <th style="width: 140px; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $index => $role)
            <tr>
                <td>{{ $roles->firstItem() + $index }}</td>
                <td style="font-weight: 600; color: #0f172a;">{{ $role->nama }}</td>
                <td><code style="font-size: 0.8rem; background: #f1f5f9; padding: 0.2rem 0.45rem; border-radius: 4px; color: #4338ca;">{{ $role->kode }}</code></td>
                <td style="color: #64748b;">{{ $role->deskripsi ?? '-' }}</td>
                <td style="text-align: right;">
                    <div class="btn-group" style="justify-content: flex-end;">
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role/jabatan ini?');" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada role/jabatan terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $roles->links() }}
</div>

@endsection