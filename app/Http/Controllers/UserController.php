<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'nullable|array|min:1',
            'roles.*' => 'string|max:50',
            'role' => 'nullable|string|max:50',
        ]);

        $roles = $request->roles ?? ($request->role ? (array) $request->role : ['admin']);
        $roles = array_values(array_unique(array_map('strtolower', $roles)));

        $academicRoles = array_intersect(['dosen', 'direktur', 'kaprodi', 'mahasiswa'], $roles);
        if (! empty($academicRoles)) {
            return back()->withErrors([
                'roles' => 'Role Dosen, Direktur, Kaprodi, dan Mahasiswa tidak dapat dibuat langsung dari Manajemen User. Silakan tambahkan user melalui menu Master Data Dosen atau Master Data Mahasiswa agar NIDN/NIM dan profil akademik terkonfigurasi dengan benar.',
            ])->withInput();
        }

        $primaryRole = in_array('admin', $roles) ? 'admin' : $roles[0];

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $primaryRole,
            'roles' => $roles,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'roles' => 'nullable|array|min:1',
            'roles.*' => 'string|max:50',
            'role' => 'nullable|string|max:50',
        ]);

        $roles = $request->roles ?? ($request->role ? (array) $request->role : $user->getRolesList());
        $roles = array_values(array_unique(array_map('strtolower', $roles)));

        $dosenRoles = array_intersect(['dosen', 'direktur', 'kaprodi'], $roles);
        if (! empty($dosenRoles) && ! $user->dosen) {
            return back()->withErrors([
                'roles' => 'User ini belum terdaftar di Master Data Dosen. Silakan daftarkan NIDN dosen terlebih dahulu pada menu Master Data Dosen sebelum memberikan role Dosen/Direktur/Kaprodi.',
            ])->withInput();
        }

        if (in_array('mahasiswa', $roles) && ! $user->mahasiswa) {
            return back()->withErrors([
                'roles' => 'User ini belum terdaftar di Master Data Mahasiswa. Silakan daftarkan NIM mahasiswa terlebih dahulu pada menu Master Data Mahasiswa sebelum memberikan role Mahasiswa.',
            ])->withInput();
        }

        $primaryRole = in_array('admin', $roles) ? 'admin' : (in_array('dosen', $roles) ? 'dosen' : (in_array('direktur', $roles) ? 'direktur' : $roles[0]));

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $primaryRole,
            'roles' => $roles,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $data['password'] = $request->password;
        }

        $user->update($data);

        if ($user->dosen) {
            $jabatan = 'Dosen';
            foreach ($roles as $r) {
                if ($r !== 'dosen' && $r !== 'admin' && $r !== 'mahasiswa') {
                    $roleModel = Role::where('kode', $r)->first();
                    $jabatan = $roleModel ? $roleModel->nama : ucfirst($r);
                    break;
                }
            }
            $user->dosen->update(['jabatan' => $jabatan]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() === 1) {
            return back()->with('error', 'Tidak dapat menghapus admin terakhir sistem.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Password akun Administrator tidak dapat di-reset melalui menu ini.');

        $defaultPassword = $user->defaultPassword();

        $user->forceFill([
            'password' => Hash::make($defaultPassword),
            'harus_ganti_password' => true,
        ])->save();

        return redirect()->route('users.index')
            ->with('success', "Password user {$user->name} berhasil di-reset ke default ({$defaultPassword}). User wajib mengganti password saat login.");
    }
}
