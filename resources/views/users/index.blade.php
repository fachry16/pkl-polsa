@extends('layouts.app')

@section('content')

    <h1 class="page-header">
        Manajemen User
    </h1>

<div class="mb-4">
    <a href="{{ route('users.create') }}"
       class="btn btn-primary">

        Tambah User

    </a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="table-container">

    <table class="data-table">

        <thead>

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($users as $index => $user)

            <tr>

                <td>
                    {{ $users->firstItem() + $index }}
                </td>

                <td>
                    {{ $user->name }}
                </td>

                <td>
                    {{ $user->email }}
                </td>

                <td>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                        @php
                            $roleMap = \App\Models\Role::all()->pluck('nama', 'kode');
                        @endphp
                        @foreach($user->getRolesList() as $role)
                            @php
                                $roleName = $roleMap->get($role) ?? ucfirst(str_replace('_', ' ', $role));
                            @endphp
                            @if($role === 'admin')
                                <span class="badge badge-diajukan">Admin</span>
                            @elseif($role === 'direktur' || str_starts_with($role, 'direktur'))
                                <span class="badge badge-disetujui">{{ $roleName }}</span>
                            @elseif($role === 'kaprodi' || str_starts_with($role, 'kaprodi'))
                                <span class="badge badge-diajukan" style="background: #e0e7ff; color: #4338ca;">{{ $roleName }}</span>
                            @else
                                <span class="badge badge-draft" style="background: #f1f5f9; color: #475569;">{{ $roleName }}</span>
                            @endif
                        @endforeach
                    </div>
                </td>

                <td>

                    <div class="btn-group">
                        <a href="{{ route('users.edit', $user->id) }}"
                           class="btn btn-sm btn-warning">

                            Edit

                        </a>

                        <x-confirm
                            action="{{ route('users.destroy', $user->id) }}"
                            method="DELETE"
                            message="Yakin ingin menghapus user ini?"
                            buttonText="Hapus"
                            confirmText="Ya, Hapus"
                        />
                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5"
                    class="text-center text-sm" style="padding: 1.5rem; color: #6b7280;">

                    Data user belum tersedia.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $users->links() }}

</div>

@endsection
