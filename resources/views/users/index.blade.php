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

    <table class="data-table data-table-stack">

        <thead>

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status Password</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            @forelse($users as $index => $user)

            <tr>

                <td data-label="No">
                    {{ $users->firstItem() + $index }}
                </td>

                <td data-label="Nama">
                    <span style="font-weight: 600; color: #0f172a;">{{ $user->name }}</span>
                </td>

                <td data-label="Email">
                    {{ $user->email }}
                </td>

                <td data-label="Role">
                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; justify-content: flex-end;">
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
                                <span class="badge badge-diajukan" style="background: #FFF3C4; color: #A16207;">{{ $roleName }}</span>
                            @else
                                <span class="badge badge-draft" style="background: #f1f5f9; color: #475569;">{{ $roleName }}</span>
                            @endif
                        @endforeach
                    </div>
                </td>

                <td data-label="Status Password">
                    @if($user->isAdmin())
                        <span class="badge badge-draft" style="background: #f1f5f9; color: #475569;">Admin</span>
                    @elseif($user->harus_ganti_password)
                        <span class="badge badge-diajukan">Wajib Ganti</span>
                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">
                            Default: <code>{{ $user->defaultPassword() }}</code>
                        </div>
                    @else
                        <span class="badge badge-disetujui">Aktif</span>
                    @endif
                </td>

                <td data-label="Aksi">

                    <div class="btn-group">
                        <a href="{{ route('users.edit', $user->id) }}"
                           class="btn btn-sm btn-warning">

                            Edit

                        </a>

                        @if(! $user->isAdmin())
                            <x-confirm
                                action="{{ route('users.reset-password', $user->id) }}"
                                method="PATCH"
                                title="Reset Password User"
                                message="Reset password user {{ $user->name }} ke password default ({{ $user->defaultPassword() }})?"
                                subMessage="User akan diwajibkan mengganti password baru saat login berikutnya."
                                buttonText="Reset"
                                buttonClass="btn btn-sm btn-secondary"
                                confirmText="Ya, Reset"
                                confirmClass="btn-secondary"
                            />
                        @endif

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

                <td colspan="6"
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
