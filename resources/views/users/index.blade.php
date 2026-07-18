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
                    @if($user->role == 'admin')

                        <span class="badge badge-diajukan">
                            Admin
                        </span>

                    @elseif($user->role == 'direktur')

                        <span class="badge badge-disetujui">
                            Direktur
                        </span>

                    @else

                        <span class="badge badge-draft">
                            Dosen
                        </span>

                    @endif

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
